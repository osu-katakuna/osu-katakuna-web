<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/leaderboard/{StrGameMode}', "API\LeaderboardController@get");
Route::get('/role/test', function() {
  dd(\App\User::find(1000)->hasPermission("katakuna.test"), \App\User::find(1001)->hasPermission("katakuna.test"));
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
