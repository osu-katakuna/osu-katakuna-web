<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientToken extends Model
{
    protected $table = "client_hashes";

    function owner() {
      return $this->belongsTo("App\User", "user_id");
    }
}
