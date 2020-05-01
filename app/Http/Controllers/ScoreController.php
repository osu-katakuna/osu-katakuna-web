<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Beatmap;
use App\BeatmapSet;
use App\Http\Controllers\BeatmapController;

class ScoreController extends Controller
{
    function get_score(Request $req) {
      // quick dirty hacks to get map name from file...
      $diff_name = explode("]", explode("[", $req->get("f"))[1])[0];
      $mapper = substr(explode(" - ", $req->get("f"))[1], strrpos(explode(" - ", $req->get("f"))[1], "(") + 1, strrpos(explode(" - ", $req->get("f"))[1], ")") - strrpos(explode(" - ", $req->get("f"))[1], "(") - 1);
      $artist = explode(" - ", $req->get("f"))[0];
      $title = substr(explode(" - ", $req->get("f"))[1], 0, strrpos(explode(" - ", $req->get("f"))[1], "(") - 1);

      $beatmap = BeatmapSet::where("filename", "=", $req->get("f"))->get()->first();

      if(!$beatmap) {
        if(!BeatmapController::DownloadBeatmap(explode(" [", $req->get("f"))[0])) {
          BeatmapController::DownloadBeatmap("$artist - $title");
        }
        $beatmap = BeatmapSet::where("filename", "=", $req->get("f"))->get()->first();
      }

      if(!$beatmap) {
        echo '-1|false';
        return;
      }

      if($beatmap->md5 != $req->get("c")) {
        echo '1|false';
        return;
      }

      $ranked_status = 2;
      $bid = $beatmap->id;
      $plays = [];

      foreach($beatmap->plays()->where("pass", "=", "1")->orderBy('score', 'desc')->get() as $p) {
        $add = true;

        foreach($plays as $i => $play) {
          if($play->player == NULL) continue;
          if($play->player->id == $p->player->id) {
            if($p->score > $play->score) {
              $plays[$i] = $p;
            }
            $add = false;
            break;
          }
        }

        if($add) {
          array_push($plays, $p);
        }
      }

      $tots = $ranked_status == 2 ? count($plays) : 0;

      echo $ranked_status . '|false|'.$bid.'|'.$bid.'|'.$tots."\n";
      echo "0\n";
      echo explode(".osu", $_GET["f"])[0] . "\n";
      echo "0.0\n\n";

      $i = 1;
      foreach($plays as $play) {
        if($play->player == NULL) continue;
        $replayID = $play->id;
        $userID = $play->player->id;
      	$playerName = $play->player->username;
      	$score = $play->score;
      	$maxCombo = $play->maxCombo;
      	$count50 = $play->count50;
      	$count100 = $play->count100;
      	$count300 = $play->count300;
      	$countMisses = $play->miss;
      	$countKatu = $play->countKatu;
      	$countGeki = $play->countGeki;
      	$fullCombo = $play->fc;
      	$mods = $play->mods;
        $rank = $i;
        $hasReplay = $play->replay_file != null ? 1 : 0;

        $actualDate = $play->created_at->timestamp;

        echo $replayID.'|'.$playerName.'|'.$score.'|'.$maxCombo.'|'.$count50.'|'.$count100.'|'.$count300.'|'.$countMisses.'|'.$countKatu.'|'.$countGeki.'|'.$fullCombo.'|'.$mods.'|'.$userID.'|'. $rank . '|' . $actualDate . '|' . $hasReplay . "\n";
        $i++;
      }
    }
}
