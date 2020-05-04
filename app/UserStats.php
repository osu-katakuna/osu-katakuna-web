<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStats extends Model
{
    protected $table = 'user_stats';

    function user() {
      return $this->belongsTo('App\User', 'user_id');
    }
}
