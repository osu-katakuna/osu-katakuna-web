<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BeatmapSet extends Model
{
    protected $table = "beatmap_sets";
    public $timestamps = false;

    function beatmap() {
      return $this->belongsTo("App\Beatmap", "beatmap_id");
    }

    function plays() {
      return $this->hasMany("App\UserPlayBeatmap", "beatmapset_id");
    }

    function positionOfUser($user) {
      $plays = [];

      foreach($this->plays()->where("pass", "=", "1")->orderBy('score', 'desc')->get() as $p) {
        $add = true;

        foreach($plays as $i => $play) {
          if($play->player->id == $p->player->id) {
            if($p->score > $play->score) {
              $plays[$i] = $p;
            }
            $add = false;
            break;
          }
        }

        if($add) {
          array_push($plays, $p);
        }
      }

      $r = 1;

      foreach($plays as $play) {
        if($play->player->id == $user->id) return $r;
        $r += 1;
      }

      return 0;
    }
}
