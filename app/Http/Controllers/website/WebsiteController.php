<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    function root(Request $req) {
      return view('website.homepage');
    }

    function register(Request $req) {
      return view('website.register');
    }

    function login(Request $req) {
      return view('website.login');
    }

    function addBeatmap(Request $req) {
      return view('website.beatmap-add');
    }

    function importReplays(Request $req) {
      return view('website.import-replays');
    }
}
