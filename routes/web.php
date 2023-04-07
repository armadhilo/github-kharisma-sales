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

Route::get('/', 'LoginController@index')->name('login');
Route::post('/login', 'LoginController@login');
Route::get('/logout','LoginController@logout');

Route::middleware(['cors','cek_login'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', function () {
            return Redirect::to('/admin/dashboard');
        });
        Route::get('/dashboard','DashboardController@index')->name('dashboard');

        Route::prefix('change-password')->group(function () {
            Route::get('/','LoginController@change_password');
            Route::post('/action-change-password','LoginController@action_change_password');
        });

        Route::prefix('master')->group(function () {
            Route::get('/get-data-all/{model}','DataMasterController@get_data_all');
            Route::get('/get-data-all-active/{model}','DataMasterController@get_data_all_active');
            Route::get('/get-data-by-id/{model}/{id}','DataMasterController@get_data_by_id');
            Route::get('/get-data-where-field-id_get/{model}/{where_field}/{id}','DataMasterController@get_data_where_field_id_get');
            Route::get('/get-data-where-field-id_first/{model}/{where_field}/{id}','DataMasterController@get_data_where_field_id_first');


            Route::get('get-provinsi-kota','DataMasterController@getProvinsiKota');
            Route::get('get-provinsi','DataMasterController@getProvinsi');
            Route::get('get-kota','DataMasterController@getKabKota');
            Route::get('get-kecamatan','DataMasterController@getKecamatan');
            Route::get('get-kelurahan','DataMasterController@getKelurahan');

            Route::get('api-provinsi','DataMasterController@api_get_province');
            Route::get('api-city-by-provinsi/{provinsi_id}','DataMasterController@api_get_city_by_province');
        });

        Route::prefix('error')->group(function () {
            Route::view('/401', 'errors.404');
            Route::view('/404', 'errors.404');
            Route::view('/under-maintenance', 'errors.under-maintenance');
        });
        
    });
});




