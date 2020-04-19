<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OsuConsts;

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

    function accuracy() {
      $accuracy = 0;

      if($this->gameMode == 0) {
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
      $raw_aim = isset($this->beatmap_set->aim) ? $this->beatmap_set->aim : 1; // se calculeaza dupa mapa FUCK
    }

    function speed() {
      $raw_speed = isset($this->beatmap_set->speed) ? $this->beatmap_set->speed : 1; // se calculeaza dupa mapa FUCCKCKCKCKCK
    }

    function acc() {
      $raw_accuracy = $this->accuracy(); // se calculeaza dupa mapa FUCKCKCKK
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

      return pow(
        pow($this->aim(), 1.1) +
        pow($this->speed(), 1.1) +
        pow($this->acc(), 1.1)
        , 1.0 / 1.1) * $multiplier;
    }
}
