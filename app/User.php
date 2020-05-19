<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $table = "users";

    protected $hidden = [
        'password_hash'
    ];

    function played_scores() {
      return $this->hasMany("App\UserPlayBeatmap", "user_id");
    }

    function currentRankingPosition($gameMode = 0) {
      $users = User::where("bot", "=", "0")->get();
      $scores = array();

      // 1. get all users total score.
      foreach($users as $user) {
        $scores[$user->id] = $user->totalScore($gameMode);
      }

      // 2. sort the scores.
      arsort($scores);

      // 3. create user ranking;
      $ranking = array();
      foreach($scores as $i => $s) {
        array_push($ranking, $i);
      }

      return array_search($this->id, $ranking) + 1;
    }

    function totalScore($gameMode = 0) {
      $score = 0;

      foreach($this->played_scores->where("pass", "=", "1") as $s) {
        if($s->gameMode != $gameMode) continue;
        $score += $s->score;
      }

      return $score;
    }

    function accuracy($gameMode = 0) {
      $accuracy = 0.0;
      $scores = $this->played_scores->where("gameMode", "=", $gameMode);

      if(count($scores) > 2) {
        foreach($scores as $s) {
          $accuracy += $s->accuracy() * 100;
        }
        $accuracy /= count($scores);
      } else if(count($scores) == 1) {
        foreach($scores as $s) {
          $accuracy += $s->accuracy() * 100;
        }
        $accuracy += 70;
        $accuracy /= count($scores) + 1;
      }

      return round($accuracy, 2);
    }

    function playCount($gamemode = 0) {
      return count($this->played_scores->where("gameMode", "=", $gamemode));
    }

    function friends() {
      return $this->belongsToMany("App\User", "user_friendships", "user", "friend");
    }

    function messages() {
      return $this->hasMany("App\PrivateMessage", "to_user_id");
    }

    function mutualFriendsWith($id) {
      if(count($this->friends) < 1) return 0;
      foreach($this->friends as $friend) {
        foreach($friend->friends as $ffriend) {
          if($ffriend->id == $this->id) return 1;
        }
      }
      return 0;
    }

    function pp($gameMode = 0) {
      $pp = 0;
      $maps = $this->played_scores;


      return $pp;
    }

    function online() {
      return count($this->sessions) >= 1;
    }

    function sessions() {
      return $this->hasMany("App\OsuUserSession", "user_id");
    }

    function stats() {
      return $this->hasMany('App\UserStats', 'user_id');
    }

    function currentStats($gamemode = 0) {
      return $this->stats()->where('gameMode', '=', $gamemode)->orderBy('created_at', 'DESC')->get()->first();
    }
}
