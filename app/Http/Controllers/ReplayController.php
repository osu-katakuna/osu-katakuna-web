<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\UserPlayBeatmap;
use App\BeatmapSet;
use App\OsuUtils;
use App\User;
use App\OsuConsts;

class ReplayController extends Controller
{
    function get(Request $req) {
      $replay = UserPlayBeatmap::find($req->get("c"));

      if($replay && $replay->replay_file != NULL) {
        return Storage::download('replays/' . $replay->replay_file);
      }
    }

    function importReplays(Request $req) {
      $errors = array();

      $rules = array();
      $replays = count($req->ReplayFile);
      foreach(range(0, $replays) as $index) {
          $rules['ReplayFile.' . $index] = 'file';
      }

      $validator = Validator::make($req->all(), $rules);
      if ($validator->fails()) {
        return redirect('/import-replays')
                    ->withErrors($validator);
      }

      foreach ($req->ReplayFile as $replay) {
        if($replay->getClientOriginalExtension() != "osr") {
          array_push($errors, "File '" . $replay->path() . "' is not an valid osu! replay file.");
          continue;
        }
        $replay_data = file_get_contents($replay->path());
        $gameMode = unpack("C", $replay_data, 0)[1];
        $clientVersion = unpack("I", $replay_data, 1)[1];
        $mapMD5 = OsuUtils::ReadStringBinary($replay_data, 5);
        $username = OsuUtils::ReadStringBinary($replay_data, 7 + strlen($mapMD5));

        $user = User::where("username", "=", $username)->get()->first();

        if(!$user) {
          array_push($errors, "Player '" . $username . "' is not registered in osu!katakuna. Could not import replay '" . $replay->getClientOriginalName() . "'!");
          continue;
        }

        if(!BeatmapSet::where("md5", "=", $mapMD5)->get()->first()) {
          array_push($errors, "Beatmap set does not exist. Could not import replay '" . $replay->getClientOriginalName() . "'!");
          continue;
        }

        $magic_string = OsuUtils::ReadStringBinary($replay_data, 9 + strlen($mapMD5) + strlen($username));
        $count300 = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string))[1];
        $count100 = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 2)[1];
        $count50 = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 4)[1];
        $countGeki = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 6)[1];
        $countKatu = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 8)[1];
        $miss = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 10)[1];
        $score = unpack('I', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 12)[1];
        $maxCombo = unpack('S', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 16)[1];
        $fullCombo = unpack('C', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 18)[1];
        $mods = unpack('I', $replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 19)[1];

        $unknown = OsuUtils::ReadStringBinary($replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 23);

        $replay_raw = substr($replay_data, 11 + strlen($mapMD5) + strlen($username) + strlen($magic_string) + 35 + 2 + strlen($unknown));
        $replay_raw = substr($replay_raw, 0, strlen($replay_raw) - 4);

        $replay_file = Str::random(40) .".osr";
        Storage::put('replays/' . $replay_file, $replay_raw);

        $play = new UserPlayBeatmap();

        $totalNotes = $count300 + $count100 + $count50 + $miss;
      	$percentage300 = $count300 / $totalNotes;
      	$percentage50 = $count50 / $totalNotes;
      	$hidden_mods = $mods & OsuConsts::Hidden || $mods & OsuConsts::Flashlight ? true : false;
        $rank = "A";

        if ($percentage300 == 1.0) {
      		if ($hidden_mods) {
      			$rank = 'XH';
      		} else {
      			$rank = 'X';
      		}
      	} else if ($percentage300 > 0.9 && $percentage50 <= 0.01 && $miss == 0) {
      		if ($hidden_mods) {
      			$rank = 'SH';
      		} else {
      			$rank = 'S';
      		}
      	} else if (($percentage300 > 0.8 && $miss == 0) || ($percentage300 > 0.9)) {
      		$rank = 'A';
      	} else if (($percentage300 > 0.7 && $miss == 0) || ($percentage300 > 0.8)) {
      		$rank = 'B';
      	} else if ($percentage300 > 0.6) {
      		$rank = 'C';
      	} else {
      		$rank = 'D';
      	}

        $play->player()->associate($user);
        $play->beatmap_set()->associate(BeatmapSet::where("md5", "=", $mapMD5)->get()->first());
        $play->count300 = $count300;
        $play->count100 = $count100;
        $play->count50 = $count50;
        $play->countGeki = $countGeki;
        $play->countKatu = $countKatu;
        $play->miss = $miss;
        $play->score = $score;
        $play->maxCombo = $maxCombo;
        $play->fc = $fullCombo;
        $play->archivedLetter = $rank;
        $play->mods = $mods;
        $play->pass = true;
        $play->gameMode = $gameMode;
        $play->state = 2;
        $play->replay_file = $replay_file;
        $play->save();
      }

      if(count($errors) > 0) {
        return redirect('/import-replays')
                    ->withErrors($errors);
      }

      return view("website.import-replays", ["message" => "Score(s) imported successfully!"]);
    }
}
