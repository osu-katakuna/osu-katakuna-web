<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    protected $table = "private_message";

    function from() {
      return $this->belongsTo("App\User", "from_user_id");
    }

    function to() {
      return $this->belongsTo("App\User", "to_user_id");
    }
}
