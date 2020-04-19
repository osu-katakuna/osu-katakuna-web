<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    function registerUser(Request $req) {
      $message = "User registered successfully!";

      $validator = Validator::make($req->all(), [
          'username' => 'required|unique:users,username|max:255',
          'email' => 'required|email',
          'password' => 'required|max:255'
      ]);

      if ($validator->fails()) {
        return redirect('/register')
                    ->withErrors($validator)
                    ->withInput();
      }

      $u = User::where("username", "=", $req->get("username"))->get();
      if(count($u) > 0) {
        $message = "The selected username is invalid!";
      } else {
        $new_user = new User();

        $new_user->username = $req->get("username");
        $new_user->email = $req->get("email");
        $new_user->password_hash = md5($req->get("password"));

        $new_user->save();
      }

      return view("website.register", ["message" => $message]);
    }
}
