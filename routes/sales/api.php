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

    Route::group(['prefix' => 'pelanggan', 'middleware' => 'validate_login'], function(){
        Route::get('/', 'Sales\PelangganController@index');
        Route::get('/{id}', 'Sales\PelangganController@detail');
        Route::post('/', 'Sales\PelangganController@store');
        Route::put('/{id}', 'Sales\PelangganController@update');
        Route::delete('/{id}', 'Sales\PelangganController@delete');
    });

});

