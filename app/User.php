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

    function clients() {
      return $this->hasMany("App\ClientToken", "user_id");
    }

    function role() {
      return $this->belongsTo("App\UserRole", "role_id");
    }

    function hasPermission($perm) {
      if(!$this->role) return false;
      if($this->role->hasPermission("*")) return true;
      if($this->role->hasPermission($perm)) return true;

      $perms_exploded = explode(".", $perm);

      for($i = 0; $i < count($perms_exploded); $i++) {
        $perm = implode(".", array_slice($perms_exploded, 0, $i + 1)) . '.*';
        if($this->role->hasPermission($perm)) return true;
      }

      return false;
    }

    function currentRankingPosition($gameMode = 0) {
      $users = User::where("bot", "=", "0")->get();
      $scores = array();

      // 1. get all users total score. (BOI DO SORTING BY PP fuck SCORE)
      foreach($users as $user) {
        $scores[$user->id] = $user->pp($gameMode);
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
      if($this->banned) return 0;

      // how to calculate Player PP:
      // get all played scores
      // get only last good score
      // add to pp

      $pp = 0;
      $plays = $this->played_scores->where("gameMode", "=", $gameMode)->where("pass", "=", "1");
      $sawMapID = array();

      foreach($plays as $play) {
        if($play->beatmap_set == NULL) continue; // maybe that beatmap doesn't exist anymore oof
        if(!in_array($play->beatmap_set->id, $sawMapID)) {
          array_push($sawMapID, $play->beatmap_set->id);

          $best_play = $this->played_scores()->where("pass", "=", "1")->where("beatmapset_id", "=", $play->beatmap_set->id)->orderBy("score", "DESC")->get()->first();
          $pp += $best_play->pp();
        }
      }

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
      return $this->stats()->where('gameMode', '=', $gamemode)->latest()->limit(1)->first();
    }
}
