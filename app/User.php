<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";

    protected $hidden = [
        'password_hash'
    ];

    function played_scores() {
      return $this->hasMany("App\UserPlayBeatmap", "user_id");
    }

    function currentRankingPosition($gameMode = 0) {
      $users = User::all();
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
      } else {
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

    function mutualFriendsWith($id) {
      if(count($this->friends) < 1) return 0;
      foreach($this->friends as $friend) {
        foreach($friend->friends as $ffriend) {
          if($ffriend->id == $this->id) return 1;
        }
      }
      return 0;
    }

    function pp() {
      $pp = 0;
      $maps = $this->played_scores;


      return $pp;
    }

    function online() {
      return count($this->hasMany("App\OsuUserSession", "user_id")->get()) >= 1;
    }
}
