<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class LeaderboardController extends Controller
{
    function get(Request $req, $StrGameMode) {
      $allowed_gamemodes = [
        "standard" => 0,
        "taiko" => 1,
        "ctb" => 2,
        "mania" => 3,
        "relax" => 4,
        "all" => -1
      ];

      if(!array_key_exists(strtolower($StrGameMode), $allowed_gamemodes)) {
        return response()->json([
          "success" => false,
          "error" => "Inacceptable game mode!"
        ]);
      }

      $currentGameMode = $allowed_gamemodes[strtolower($StrGameMode)];
      $leaderboard = array();

      if($currentGameMode != -1) {
        $scores = array();

        foreach(User::where("bot", "=", "0")->get() as $user) {
          $scores[$user->id] = $user->totalScore($currentGameMode);
        }

        arsort($scores);

        $rank = 1;

        foreach($scores as $i => $s) {
          $user = User::find($i);

          array_push($leaderboard, [
            "id" => $user->id,
            "rank" => $rank,
            "username" => $user->username,
            "pp" => $user->pp($currentGameMode),
            "score" => $user->totalScore($currentGameMode),
            "accuracy" => $user->accuracy($currentGameMode),
            "plays" => $user->playCount($currentGameMode)
          ]);

          $rank++;
        }
      } else {
        foreach($allowed_gamemodes as $gamemode => $id) {
          if($id == -1) continue;

          $leaderboard[$gamemode] = array();
          $scores = array();

          foreach(User::where("bot", "=", "0")->get() as $user) {
            $scores[$user->id] = $user->totalScore($id);
          }

          arsort($scores);

          $rank = 1;

          foreach($scores as $i => $s) {
            $user = User::find($i);

            array_push($leaderboard[$gamemode], [
              "id" => $user->id,
              "rank" => $rank,
              "username" => $user->username,
              "pp" => $user->pp($id),
              "score" => $user->totalScore($id),
              "accuracy" => $user->accuracy($id),
              "plays" => $user->playCount($id)
            ]);

            $rank++;
          }
        }
      }

      return response()->json([
        "success" => true,
        "leaderboard" => $leaderboard
      ]);
    }
}
