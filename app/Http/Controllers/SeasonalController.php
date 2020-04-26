<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeasonalController extends Controller
{
    function getSeasonal(Request $req) {
      $bgs = array();

      foreach(Storage::files("seasonal") as $file) {
        array_push($bgs, "https://osu.ppy.sh/backgrounds/" . pathinfo($file, PATHINFO_FILENAME) . "." . pathinfo($file, PATHINFO_EXTENSION));
      }

      return response()->json($bgs);
    }

    function getBackground(Request $req, $bg) {
      if(Storage::exists("seasonal/" . $bg)) {
        return Storage::download("seasonal/" . $bg);
      }
    }
}
