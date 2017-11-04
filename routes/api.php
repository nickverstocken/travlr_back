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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'cors', 'prefix' => 'v1'], function(){
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('recover', 'AuthController@recover');
});
Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'cors']], function() {
    Route::get('logout', 'AuthController@logout');
    Route::get('currentUser', [
        'as' => 'user.showCurrent',
        'uses' => 'UserController@showCurrent'
    ]);
    Route::get('user/{id}', [
        'as' => 'user.show',
        'uses' => 'UserController@show'
    ]);
    Route::get('trips', [
        'as' => 'trips.index',
        'uses' => 'TripsController@index'
    ]);
    Route::get('trips/{id}', [
        'as' => 'trips.show',
        'uses' => 'TripsController@show'
    ]);
    Route::post('trips/{id}', [
        'as' => 'trips.store',
        'uses' => 'TripsController@store'
    ]);
    Route::post('trips/update/{id}', [
        'as' => 'trips.update',
        'uses' => 'TripsController@update'
    ]);
    Route::post('trips/cover/{id}', [
        'as' => 'trips.updateCoverImage',
        'uses' => 'TripsController@updateCoverImage'
    ]);
    Route::delete('trips/{id}', [
        'as' => 'trips.destroy',
        'uses' => 'TripsController@destroy'
    ]);
    Route::post('trips/{tripid}/stops', [
        'as' => 'stops.store',
        'uses' => 'StopController@store'
    ]);
    Route::post('/stop/{stopId}/saveImages', [
        'as' => 'stops.saveImages',
        'uses' => 'StopController@saveImages'
    ]);
    //updateCoverImage
});

