<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IPController extends Controller
{
    function getIP(Request $req) {
      return json_decode(file_get_contents("https://ipinfo.io/json"))->ip;
    }
}
