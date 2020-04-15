<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beatmap extends Model
{
    protected $table = "beatmaps";
    public $timestamps = false;

    function sets() {
      return $this->belongsToMany("App/BeatmapSet");
    }
}
