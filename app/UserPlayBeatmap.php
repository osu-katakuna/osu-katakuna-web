<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OsuConsts;

class UserPlayBeatmap extends Model
{
    protected $table = "user_plays";

    function beatmap_set() {
      return $this->belongsTo("App\BeatmapSet", "beatmapset_id");
    }

    function player() {
      return $this->belongsTo("App\User", "user_id");
    }

    // Source: https://github.com/ppy/osu-performance/blob/master/src/performance/osu/OsuScore.cpp

    function total_hits() {
      return $this->count300 + $this->count100 + $this->count50 + $this->miss;
    }

    function total_successful_hits() {
      return $this->count300 + $this->count100 + $this->count50;
    }

    function accuracy() {
      if($this->total_hits() == 0) return 0;
      $score = ($this->count300 * 300) + ($this->count100 * 100) + ($this->count50 * 50);
      return max(0.0, min(1.0, $score / ($this->total_hits() * 300)));
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
