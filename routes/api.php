<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::resource('cal', 'Admin\gCalendarController');
Route::get('oauth', 'Admin\gCalendarController@oauth')->name('oauthCallback');
//Route::resource('gcalendar', 'Admin\gCalendarController');
 //		  Route::get('oauth', [ 'uses' => 'Admin\gCalendarController@oauth','as' => 'oauthCallback']);
