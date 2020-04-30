<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    function root(Request $req) {
      return view('welcome');
    }

    function register(Request $req) {
      return view('website.register');
    }

    function addBeatmap(Request $req) {
      return view('website.beatmap-add');
    }

    function importReplays(Request $req) {
      return view('website.import-replays');
    }
}
