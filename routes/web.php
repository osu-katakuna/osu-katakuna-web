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
    return view('website.register');
});
Route::post('/register', "RegistrationController@registerUser");

Route::get('/add-beatmap', function () {
    return view('website.beatmap-add');
});
Route::post('/add-beatmap', "BeatmapController@registerUploadedBeatmap");

Route::get('/import-replays', function () {
  return view('website.import-replays');
});
Route::post('/import-replays', "ReplayController@importReplays");

Route::get('/forcebeatmap', "BeatmapController@forceRegister");

Route::get('/web/osu-search.php', "BeatmapController@search");
Route::get('/web/osu-search-set.php', "BeatmapController@set_search");
Route::get('/web/osu-getreplay.php', "ReplayController@get");
Route::get('/web/osu-getseasonal.php', "SeasonalController@getSeasonal");
Route::get('/backgrounds/{bg}', "SeasonalController@getBackground");
Route::get('/web/maps/{map}', "BeatmapController@StreamMap");
Route::get('/d/{id}', "BeatmapController@download");
Route::get('/u/{id}', "website\UserController@userProfile");
Route::get('/thumb/{id}l.jpg', "BeatmapController@thumbnail_large");
Route::get('/thumb/{id}.jpg', "BeatmapController@thumbnail");
Route::get('/preview/{id}.mp3', "BeatmapController@song_preview");

Route::post('/web/osu-submit-modular-selector.php', "BeatmapController@submit_score");
Route::get('/web/osu-osz2-getscores.php', "ScoreController@get_score");
