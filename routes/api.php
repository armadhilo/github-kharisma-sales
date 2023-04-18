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

Route::get('/provinsi', 'DataMasterController@getProvinsi');
Route::get('/kabkota/{provinsi_id}', 'DataMasterController@getKabKota');
Route::get('/kecamatan/{kota_id}', 'DataMasterController@getKecamatan');
Route::get('/kelurahan/{kecamatan_id}', 'DataMasterController@getKelurahan');

Route::get('/provinsi-raja-ongkir', 'DataMasterController@api_get_province_raja_ongkir');
Route::get('/city-raja-ongkir/{provinsi_id}', 'DataMasterController@api_get_city_by_province_raja_ongkir');
