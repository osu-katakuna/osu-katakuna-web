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
Route::get('/', 'website\WebsiteController@root')->name('home');
Route::get('/home', 'website\WebsiteController@root')->name('home');

// ========== START OF Administration Routes =============

Route::middleware(['hasPermission:admin.dashboard'])->group(function () {
  Route::get('/admin', 'website\WebsiteController@dashboard')->name('admin');

  Route::middleware(['hasPermission:admin.beatmap.manage'])->group(function () {
    Route::get('/admin/beatmaps/import', 'website\WebsiteController@addBeatmap')->middleware('hasPermission:admin.beatmap.manage.add')->name('beatmaps.add');
    Route::post('/admin/beatmaps/import', "BeatmapController@registerUploadedBeatmap")->middleware('hasPermission:admin.beatmap.manage.add')->name('beatmaps.add');

    Route::get('/admin/beatmaps/manage', 'website\WebsiteController@manageBeatmap')->middleware('hasPermission:admin.beatmap.manage')->name('beatmaps.manage');
  });

  Route::middleware(['hasPermission:admin.plays.manage'])->group(function () {
    Route::get('/import-replays', 'website\WebsiteController@importReplays')->middleware('hasPermission:admin.plays.manage.import')->name('import-replays');
    Route::post('/import-replays', "ReplayController@importReplays")->middleware('hasPermission:admin.plays.manage.import')->name('import-replays');
  });

  Route::middleware(['hasPermission:admin.users.manage'])->group(function () {
    Route::get('/admin/ban/user/{id}', "UserController@banUser")->middleware('hasPermission:admin.users.manage.ban');
    Route::get('/admin/unban/user/{id}', "UserController@unbanUser")->middleware('hasPermission:admin.users.manage.unban');
    Route::get('/admin/delete/user/{id}', "UserController@deleteUser")->middleware('hasPermission:admin.users.manage.delete');
  });
});

// ========== END OF Administration Routes ================

Route::get('/register', 'website\WebsiteController@register')->name('register');
Route::post('/register', "RegistrationController@registerUser")->name('register');

Route::get('/login', 'website\WebsiteController@login')->name('login');
Route::post('/login', "website\UserController@login")->name('login');

Route::get('/logout', 'website\UserController@logout');

Route::get('/leaderboard', function() {
  return view('website.leaderboard');
})->name("leaderboard");

Route::get('/query-server-ip', "IPController@getIP");

Route::get('/web/osu-search.php', "BeatmapController@search");
Route::get('/web/osu-search-set.php', "BeatmapController@set_search");
Route::get('/web/osu-getreplay.php', "ReplayController@get");
Route::get('/web/osu-getseasonal.php', "SeasonalController@getSeasonal");
Route::get('/backgrounds/{bg}', "SeasonalController@getBackground");
Route::get('/web/maps/{map}', "BeatmapController@StreamMap");
Route::get('/d/{id}', "BeatmapController@download");
Route::get('/u/{id}', "website\UserController@userProfile")->name('user');
Route::get('/users/{id}', "website\UserController@userProfile");
Route::get('/thumb/{id}l.jpg', "BeatmapController@thumbnail_large");
Route::get('/thumb/{id}.jpg', "BeatmapController@thumbnail");
Route::get('/preview/{id}.mp3', "BeatmapController@song_preview");

Route::get('/get/replay/{id}', "ReplayController@getFull");

Route::post('/web/osu-submit-modular-selector.php', "BeatmapController@submit_score");
Route::post('/web/osu-submit-modular.php', "BeatmapController@submit_score");
Route::get('/web/osu-osz2-getscores.php', "ScoreController@get_score");

Route::post('/users', 'RegistrationController@osuRegisterUser');
