<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScoreController extends Controller
{
    function osuDateToUNIXTimestamp($date) {
      return $date; // WIP
    }

    function get_score(Request $req) {
      $ranked_status = 2;
      $bid = $_GET["i"];
      $tots = 0;

      $replayID = 0;
      $userID = 1000;
    	$playerName = "OrezCuLapte";
    	$score = 69696969;
    	$maxCombo = 1651;
    	$count50 = 0;
    	$count100 = 0;
    	$count300 = 1651;
    	$countMisses = 0;
    	$countKatu = 1651;
    	$countGeki = 1651;
    	$fullCombo = 1;
    	$mods = 0;
      $rank = 1;
      $hasReplay = 1;
      $actualDate = $this->osuDateToUNIXTimestamp("200327181200");

      echo $ranked_status . '|false|'.$bid.'|'.$bid.'|'.$tots."\n";
      echo "0\n";
      echo explode(".osu", $_GET["f"])[0] . "\n";
      echo "0.0\n\n";
      echo $replayID.'|'.$playerName.'|'.$score.'|'.$maxCombo.'|'.$count50.'|'.$count100.'|'.$count300.'|'.$countMisses.'|'.$countKatu.'|'.$countGeki.'|'.$fullCombo.'|'.$mods.'|'.$userID.'|'.$rank. '|' . $actualDate . '|' . $hasReplay . chr(10);
    }
}
