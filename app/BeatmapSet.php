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
}
