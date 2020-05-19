<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Illuminate\Support\Facades\Storage;
use \App\PrivateMessage;

class UserController extends Controller
{
  
    function banUser(Request $request, $id) {
      $user = User::find($id);

      if(!$user) {
        return "User could not be found!";
      }

      $user->banned = true;
      $user->delete();

      $user->save();

      return "User " . $user->username . " has been banned!";
    }

    function unbanUser(Request $request, $id) {
      $user = User::withTrashed()->find($id);

      if(!$user) {
        return "User could not be found!";
      }

      $user->banned = false;
      $user->restore();

      $user->save();

      return "User " . $user->username . " has been unbanned!";
    }

    function deleteUser(Request $request, $id) {
      $user = User::withTrashed()->find($id);

      if(!$user) {
        return "User could not be found!";
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

      return "User " . $user->username . " has been deleted!";
    }
}
