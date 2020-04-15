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

    function playCount() {
      return count($this->played_scores);
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
}
