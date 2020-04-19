<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\UserPlayBeatmap;

class ReplayController extends Controller
{
    function get(Request $req) {
      $replay = UserPlayBeatmap::find($req->get("c"));

      if($replay && $replay->replay_file != NULL) {
        return Storage::download('replays/' . $replay->replay_file);
      }
    }
}
