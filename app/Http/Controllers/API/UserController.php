<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\PrivateMessage;
use App\User;

class UserController extends Controller
{
    function all(Request $req) {
      $users = array();

      foreach(User::withTrashed()->get() as $user) {
        $u = $user;
        $u["ownerBadge"] = $user->hasPermission("users.badge.owner");
        $u["developerBadge"] = $user->hasPermission("users.badge.developer");
        $u["bugHunterBadge"] = $user->hasPermission("users.badge.bug-hunter");
        $u["moderatorBadge"] = $user->hasPermission("users.badge.moderator");

        array_push($users, $u);
      }

      return [
        "error" => false,
        "users" => $users
      ];
    }

    function ban(Request $request, $id) {
      $user = User::withTrashed()->find($id);

      if(!$user) {
        return [
          "error" => true,
          "message" => "User could not be found."
        ];
      }

      if($user->banned) {
        return [
          "error" => true,
          "message" => $user->username . " is already banned."
        ];
      }

      $user->banned = true;

      $user->save();

      return [
        "error" => false
      ];
    }

    function pardon(Request $request, $id) {
      $user = User::withTrashed()->find($id);

      if(!$user) {
        return [
          "error" => true,
          "message" => "User could not be found."
        ];
      }

      if(!$user->banned) {
        return [
          "error" => true,
          "message" => $user->username . " is already pardoned."
        ];
      }

      $user->banned = false;
      $user->restore();

      $user->save();

      return [
        "error" => false
      ];
    }

    function delete(Request $request, $id) {
      $user = User::withTrashed()->find($id);

      if(!$user) {
        return [
          "error" => true,
          "message" => "User could not be found."
        ];
      }

      foreach($user->played_scores as $score) {
        Storage::delete('replays/' . $score->replay_file);
        $score->delete();
      }

      \DB::delete("DELETE FROM user_friendships WHERE friend = ? OR user = ?", [$user->id, $user->id]);

      foreach(PrivateMessage::where("from_user_id", "=", $user->id)->get() as $msg) {
        $msg->delete();
      }

      foreach(PrivateMessage::where("to_user_id", "=", $user->id)->get() as $msg) {
        $msg->delete();
      }

      foreach($user->stats as $stats) {
        $stats->delete();
      }

      \DB::delete("DELETE FROM tokens WHERE user_id = ?", [$user->id]);

      $user->forceDelete();

      return [
        "error" => false
      ];
    }
}
