<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\ClientToken;

class BanchoClientController extends Controller
{
    function connect(Request $req) {
      $validator = Validator::make($req->all(), [
          'u' => 'required|exists:users,username',
          'h' => 'required|max:255',
          'v' => 'required|max:255',
          'ch' => 'required|max:255'
      ]);

      if ($validator->fails()) {
        return "";
      }

      $u = User::where([
        ["username", "=", $req->get('u')],
        ["password_hash", "=", hash("sha256", $req->get('h'))]
      ])->get()->first();

      if($u == NULL) {
        return "";
      }

      if($u->clients()->where("hash", "=", $req->get('ch'))->get()->first() == NULL) {
        return "error: verify";
      }

      return $u->country;
    }

    function addClient(Request $req) {
      if(\Auth::user() == NULL) {
        return view("website.client-verification-failed", ["message" => "you are not connected"]);
      }

      if(!isset($_GET['ch'])) {
        return view("website.client-verification-failed", ["message" => "missing parameters"]);
      }

      if(count(explode(":", $req->get('ch'))) < 6) {
        return view("website.client-verification-failed", ["message" => "invalid client hash"]);
      }

      // to do: readd client hashes

      if(\Auth::user()->clients()->where("hash", "=", $req->get('ch'))->get()->first() != NULL) {
        return view("website.client-verification-failed", ["message" => "this client has been already registered"]);
      }

      $token = new ClientToken;
      $token->hash = $req->get("ch");
      $token->owner()->associate(\Auth::user());
      $token->save();

      return view("website.client-verified");
    }
}
