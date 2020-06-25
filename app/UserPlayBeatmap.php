<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\OsuConsts;

class UserPlayBeatmap extends Model
{
    protected $table = "user_plays";
    protected $dates = ['created_at', 'updated_at'];

    function beatmap_set() {
      return $this->belongsTo("App\BeatmapSet", "beatmapset_id");
    }

    function player() {
      return $this->belongsTo("App\User", "user_id");
    }

    // Source: https://github.com/ppy/osu-performance/blob/master/src/performance/osu/OsuScore.cpp

    function totalHits() {
      if($this->gameMode == 0 || $this->gameMode == 4) {
        return $this->count300 + $this->count100 + $this->count50 + $this->miss;
      } else if($this->gameMode == 1) {
        return $this->miss + $this->count100 + $this->count300;
      } else if($this->gameMode == 2) {
        return $this->count300 + $this->count100 + $this->count50 + $this->miss + $this->countKatu;
      } else if($this->gameMode == 3) {
        return $this->miss + $this->count50 + $this->count100 + $this->count300 + $this->countGeki + $this->countKatu;
      }
    }

    function accuracy() {
      $accuracy = 0;

      if($this->gameMode == 0 || $this->gameMode == 4) {
        // standard
        $totalPoints = $this->count50 * 50 + $this->count100 * 100 + $this->count300 * 300;
			  $totalHits = $this->count300 + $this->count100 + $this->count50 + $this->miss;
        if($totalHits == 0) {
          $accuracy = 1;
        } else {
          $accuracy = $totalPoints / ($totalHits * 300);
        }
      } else if($this->gameMode == 1) {
        // taiko
        $totalPoints = ($this->count100 * 50) + ($this->count300 * 100);
			  $totalHits = $this->miss + $this->count100 + $this->count300;
        if($totalHits == 0) {
          $accuracy = 1;
        } else {
          $accuracy = $totalPoints / ($totalHits * 100);
        }
      } else if($this->gameMode == 2) {
        // catch the beat
        $fruits = $this->count300 + $this->count100 + $this->count50;
			  $totalFruits = $fruits + $this->miss + $this->countKatu;
        if($totalFruits == 0) {
          $accuracy = 1;
        } else {
          $accuracy = $fruits / $totalFruits;
        }
      } else if($this->gameMode == 3) {
        // mania
        $totalPoints = $this->count50 * 50 + $this->count100 * 100 + $this->countKatu * 200 + $this->count300 * 300 + $this->countGeki * 300;
			  $totalHits = $this->miss + $this->count50 + $this->count100 + $this->count300 + $this->countGeki + $this->countKatu;
        $accuracy = $totalPoints / ($totalHits * 300);
      }

      return max(0.0, min(1.0, $accuracy));
    }

    function aim() {
      if(!isset($this->beatmap_set->aim)) return 0;

      $rawAim = $this->beatmap_set->aim == 0 ? 1 : $this->beatmap_set->aim;

      if (($this->mods & OsuConsts::TouchDevice) > 0) {
    		$rawAim = pow($rawAim, 0.8);
      }

      $aimValue = pow(5.0 * max(1.0, $rawAim / 0.0675) - 4.0, 3.0) / 100000.0;

      $totalHits = $this->totalHits();

      // Longer maps are worth more
      $lengthBonus = 0.95 + 0.4 * min(1.0, $totalHits / 2000.0) + ($totalHits > 2000 ? log10($totalHits / 2000.0) * 0.5 : 0.0);
      $aimValue *= $lengthBonus;

      // Penalize misses exponentially. This mainly fixes tag4 maps and the likes until a per-hitobject solution is available
      $aimValue *= pow(0.97, $this->miss);

      // Combo scaling
      $maxCombo = $this->maxCombo; // all user max combo
      $_maxCombo = $totalHits;

      if ($maxCombo > 0) {
        $aimValue *= min((pow($_maxCombo, 0.8) / pow($maxCombo, 0.8)), 1.0);
      }

      $approachRate = $this->beatmap_set->ar;
    	$approachRateFactor = 1.0;
    	if ($approachRate > 10.33) {
    		$approachRateFactor += 0.3 * ($approachRate - 10.33);
    	} else if ($approachRate < 8.0) {
    		$approachRateFactor += 0.01 * (8.0 - $approachRate);
    	}

      $aimValue *= $approachRateFactor;

      // We want to give more reward for lower AR when it comes to aim and HD. This nerfs high AR and buffs lower AR.
      if (($this->mods & OsuConsts::Hidden) > 0) $aimValue *= 1.0 + 0.04 * (12.0 - $this->beatmap_set->ar);

      if (($this->mods & OsuConsts::Flashlight) > 0) {
		    $aimValue *= 1.0 + 0.35 * min(1.0, $totalHits / 200.0) +
         		($totalHits > 200 ? 0.3 * min(1.0, $totalHits - 200 / 300.0) +
         		($totalHits > 500 ? $totalHits - 500 / 1200.0 : 0.0) : 0.0);
      }

      // Scale the aim value with accuracy _slightly_
    	$aimValue *= 0.5 + $this->accuracy() / 2.0;
    	// It is important to also consider accuracy difficulty when doing that
    	$aimValue *= 0.98 + (pow($this->beatmap_set->od, 2) / 2500);

      return $aimValue;
    }

    function speed() {
      if(!isset($this->beatmap_set->speed)) return 0;

      $raw_speed = $this->beatmap_set->speed == 0 ? 1 : $this->beatmap_set->speed;

      $speedValue = pow(5.0 * max(1.0, $raw_speed / 0.0675) - 4.0, 3.0) / 100000.0;

      $totalHits = $this->totalHits();

    	$approachRate = $this->beatmap_set->ar;
    	$approachRateFactor = 1.0;
    	if ($approachRate > 10.33) $approachRateFactor += 0.3 * ($approachRate - 10.33);

    	$speedValue *= $approachRateFactor;

      // Longer maps are worth more
    	$speedValue *= 0.95 + 0.4 * min(1.0, $totalHits / 2000.0) + ($totalHits > 2000 ? log10($totalHits / 2000.0) * 0.5 : 0.0);

      // Penalize misses exponentially. This mainly fixes tag4 maps and the likes until a per-hitobject solution is available
    	$speedValue *= pow(0.97, $this->miss);

      // Combo scaling
      $maxCombo = $this->maxCombo;
    	$_maxCombo = $totalHits;

    	if ($maxCombo > 0) $speedValue *= min(pow($_maxCombo, 0.8) / pow($maxCombo, 0.8), 1.0);

      // We want to give more reward for lower AR when it comes to speed and HD. This nerfs high AR and buffs lower AR.
    	if (($this->mods & OsuConsts::Hidden) > 0) $speedValue *= 1.0 + 0.04 * (12.0 - $approachRate);

      // Scale the speed value with accuracy _slightly_
    	$speedValue *= 0.02 + $this->accuracy();
    	// It is important to also consider accuracy difficulty when doing that
    	$speedValue *= 0.96 + (pow($this->beatmap_set->od, 2) / 1600);

      return $speedValue;
    }

    function acc() {
      if(!isset($this->beatmap_set->aim) || !isset($this->beatmap_set->speed)) return 0;

      $raw_accuracy = $this->accuracy();
      $totalHits = $this->totalHits();

      // SCORE v1 CODE!!

      $numHitObjectsWithAccuracy = $totalHits;
  		if ($numHitObjectsWithAccuracy > 0) $betterAccuracyPercentage = ($this->count300 - ($totalHits - $numHitObjectsWithAccuracy) * 6 + $this->count100 * 2 + $this->count50) / ($numHitObjectsWithAccuracy * 6);
  		else $betterAccuracyPercentage = 0;

  		// It is possible to reach a negative accuracy with this formula. Cap it at zero - zero points
  		if ($betterAccuracyPercentage < 0) $betterAccuracyPercentage = 0;

      // Lots of arbitrary values from testing.
    	// Considering to use derivation from perfect accuracy in a probabilistic manner - assume normal distribution
    	$accValue = pow(1.52163, $this->beatmap_set->od) * pow($betterAccuracyPercentage, 24) * 2.83;

      // Bonus for many hitcircles - it's harder to keep good accuracy up for longer
	    $accValue *= min(1.15, pow($numHitObjectsWithAccuracy / 1000.0, 0.3));

      if (($this->mods & OsuConsts::Hidden) > 0) $accValue *= 1.08;

    	if (($this->mods & OsuConsts::Flashlight) > 0) $accValue *= 1.02;

      return $accValue;
    }

    function pp() {
      // unranked mods = pp 0
      if(($this->mods & OsuConsts::Relax) > 0 ||
         ($this->mods & OsuConsts::Relax2) > 0 ||
         ($this->mods & OsuConsts::Autoplay) > 0
      ) return 0;

      $multiplier = 1.12;
      if (($this->mods & OsuConsts::NoFail) > 0) $multiplier *= 0.90;
    	if (($this->mods & OsuConsts::SpunOut) > 0) $multiplier *= 0.95;

      $rawPP = pow(
        pow($this->aim(), 1.1) +
        pow($this->speed(), 1.1) +
        pow($this->acc(), 1.1)
        , 1.0 / 1.1) * $multiplier;

      return ceil(round($rawPP, 2));
    }
}
