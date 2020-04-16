<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', "RegistrationController@registerUser");

Route::get('/web/osu-search.php', "BeatmapController@search");
Route::get('/web/osu-search-set.php', "BeatmapController@set_search");
Route::get('/d/{id}', "BeatmapController@download");
Route::get('/thumb/{id}l.jpg', "BeatmapController@thumbnail_large");
Route::get('/thumb/{id}.jpg', "BeatmapController@thumbnail");
Route::get('/preview/{id}.mp3', "BeatmapController@song_preview");

Route::post('/web/osu-submit-modular-selector.php', "BeatmapController@submit_score");
Route::get('/web/osu-osz2-getscores.php', "ScoreController@get_score");

Route::get("/player/info/{id}", function($id) {
  return \App\User::find($id)->played_scores[9]->accuracy();
});
