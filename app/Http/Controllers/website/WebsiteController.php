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
      $req->session()->keep(['redirect']);
      return view('website.login');
    }

    function addBeatmap(Request $req) {
      return view('admin.beatmap-add');
    }

    function manageBeatmap(Request $req) {
      return view('admin.beatmap-manage');
    }

    function importReplays(Request $req) {
      return view('admin.import-replays');
    }

    function dashboard(Request $req) {
      return view('admin.components.page');
    }
}
