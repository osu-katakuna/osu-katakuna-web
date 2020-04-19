<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    function userProfile(Request $req, $id) {
      if(!User::find($id)) return view("website.notFound");

      return view("website.profile", [
        "user" => User::find($id)
      ]);
    }
}
