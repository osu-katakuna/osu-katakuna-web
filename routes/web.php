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

  // users routes
  Route::middleware(['hasPermission:admin.users.manage'])->group(function () {
    Route::get('/admin/api/users', "API\UserController@all")->middleware("hasPermission:admin.users.manage");
    Route::get('/admin/api/users/ban/{id}', "API\UserController@ban")->middleware('hasPermission:admin.users.manage.ban');
    Route::get('/admin/api/users/pardon/{id}', "API\UserController@pardon")->middleware('hasPermission:admin.users.manage.pardon');
    Route::get('/admin/api/users/remove/{id}', "API\UserController@delete")->middleware('hasPermission:admin.users.manage.delete');
    Route::get('/admin/users/manage', "website\WebsiteController@users")->middleware('hasPermission:admin.users.manage')->name('users.manage');
  });
});

// ========== END OF Administration Routes ================

// register users on the website
Route::get('/register', 'website\WebsiteController@register')->name('register');
Route::post('/register', "RegistrationController@registerUser")->name('register');

// login on the website
Route::get('/login', 'website\WebsiteController@login')->name('login');
Route::post('/login', "website\UserController@login")->name('login');

// end session
Route::get('/logout', 'website\UserController@logout')->name('logout');

// ========== website routes ==========

Route::get('/leaderboard', function() {
  return view('website.leaderboard');
})->name("leaderboard"); // get the leaderboard page

Route::get('/query-server-ip', "IPController@getIP"); // used for the switcher

// ========== end ==========

// osu! direct
Route::get('/web/osu-search.php', "BeatmapController@search"); // search for beatmaps on osu! direct
Route::get('/web/osu-search-set.php', "BeatmapController@set_search"); // search for a specific set on osu! direct(used to download sets)

Route::get('/web/osu-getreplay.php', "ReplayController@get"); // get replay of score
Route::get('/web/osu-getseasonal.php', "SeasonalController@getSeasonal"); // get seasonal backgrounds... ah yeah :D


Route::get('/backgrounds/{bg}', "SeasonalController@getBackground");

Route::get('/web/maps/{map}', "BeatmapController@StreamMap"); // download a specific beatmap(only for updates)

Route::get('/d/{id}', "BeatmapController@download"); // download the beatmaps

Route::get('/u/{id}', "website\UserController@userProfile")->name('user'); // get profile of user

Route::get('/spectate/{id}', "website\UserController@userSpectate");
Route::get('/users/{id}', "website\UserController@userProfile");

Route::get('/thumb/{id}l.jpg', "BeatmapController@thumbnail_large"); // get large preview image of beatmap
Route::get('/thumb/{id}.jpg', "BeatmapController@thumbnail"); // get preview image of beatmap
Route::get('/preview/{id}.mp3', "BeatmapController@song_preview"); // get preview music of beatmap

Route::get('/get/replay/{id}', "ReplayController@getFull");

Route::post('/web/osu-submit-modular-selector.php', "BeatmapController@submit_score"); // submit scores to server
Route::post('/web/osu-submit-modular.php', "BeatmapController@submit_score"); // submit scores to server in older versions of osu!
Route::get('/web/osu-osz2-getscores.php', "ScoreController@get_score"); // get scoreboard
Route::get('/web/bancho_connect.php', "BanchoClientController@connect"); // connect to bancho?!

Route::get('/client-verifications/create', "BanchoClientController@addClient")->middleware("loginRequired"); // add client to account
Route::get('/p/verify', "BanchoClientController@addClient")->middleware("loginRequired");  // add client to account(for older versions of osu!)

Route::post('/users', 'RegistrationController@osuRegisterUser'); // register from the client(only in latest osu!)
