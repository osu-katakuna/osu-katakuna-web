<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function userProfile(Request $req, $id) {
      if(!User::find($id)) return view("website.user-not-found");

      return view("website.profile", [
        "user" => User::find($id)
      ]);
    }

    function login(Request $request) {
      $credentials = $request->only('username', 'password');

      $validator = Validator::make($request->all(), [
          'username' => 'required|exists:users,username|max:20',
          'password' => 'required|max:255'
      ]);

      if ($validator->fails()) {
        return redirect("login")
                    ->withErrors($validator)
                    ->withInput();
      }

      if(strlen($request->get("username")) != strlen(utf8_decode($request->get("username")))) {
        return redirect("login")->withErrors("Invalid username.")->withInput();
      }

      $user = User::where("username", "=", $request->get("username"))->orWhere("email", "=", $request->get("username"))->get()->first();
      if($user) {
        if(md5($request->get("password")) === $user->password_hash) {
          Auth::login($user);
          return redirect("/");
        } else {
          return redirect("login")->withErrors("Wrong password.")->withInput();
        }
      }
      return redirect("login")->withErrors("Unknown user.")->withInput();
    }

    function logout(Request $request) {
      Auth::logout();
      return redirect('home');
    }
}
