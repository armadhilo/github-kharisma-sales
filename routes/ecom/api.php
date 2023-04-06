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

Route::group(['prefix' => 'ecom'], function(){
    Route::post('/login', 'Ecom\AuthController@login');

    Route::group(['middleware' => 'validate_login'], function(){
        Route::post('/logout', 'Ecom\AuthController@logout');

        Route::group(['prefix' => 'alamat-pengiriman'], function(){
            Route::get('/', 'Ecom\AlamatPengirimanController@index');
            Route::get('/{id}', 'Ecom\AlamatPengirimanController@detail');
            Route::post('/', 'Ecom\AlamatPengirimanController@store_update');
            Route::delete('/{id}', 'Ecom\AlamatPengirimanController@delete');

            Route::get('/setting-alamat-utama/{id}', 'Ecom\AlamatPengirimanController@setting_alamat_utama');
        });
    });
});

