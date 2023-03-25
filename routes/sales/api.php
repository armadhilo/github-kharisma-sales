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

Route::get('/try', 'DataMasterController@try_code');

Route::group(['prefix' => 'sales'], function(){
    Route::post('/login', 'Sales\AuthController@login');

    Route::group(['middleware' => 'validate_login'], function(){
        Route::post('/logout', 'Sales\AuthController@logout');
    });
});

