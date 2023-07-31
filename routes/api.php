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
Route::get('/city-raja-ongkir/{provinsi_id}', 'DataMasterController@api_get_city_raja_ongkir');
Route::get('/kecamatan-raja-ongkir/{city_id}', 'DataMasterController@api_get_kecamatan_raja_ongkir');

Route::get('/call-center', 'DataMasterController@get_call_center');

Route::group(['prefix' => 'odoo'], function(){
    Route::get('/try', 'OdooController@try_code');
    Route::post('/sync-product', 'OdooController@sync_product');
    Route::get('/sync-pelanggan', 'OdooController@sync_pelanggan');
    Route::get('/sync-status', 'OdooController@sync_status');
});

Route::group(['prefix' => 'sync-odoo'], function(){
    Route::get('/try', 'OdooController@try_code');

    Route::group(['prefix' => 'product'], function(){
        Route::post('/store-update', 'SyncOdoo\ProductController@store_update');
        Route::delete('/{product_id}', 'SyncOdoo\ProductController@delete');
    });

    Route::group(['prefix' => 'pelanggan'], function(){
        Route::post('/store-update', 'SyncOdoo\PelangganController@store_update');
        Route::delete('/{pelanggan_id}', 'SyncOdoo\PelangganController@delete');
    });

    Route::group(['prefix' => 'product-tag'], function(){
        Route::post('/store-update', 'SyncOdoo\ProductTagController@store_update');
    });

    Route::group(['prefix' => 'product-uom'], function(){
        Route::post('/store-update', 'SyncOdoo\ProductUomController@store_update');
        Route::delete('/{product_tmpl_id}/{product_uom_id}', 'SyncOdoo\ProductUomController@delete');
    });

    Route::group(['prefix' => 'product-pricelist'], function(){
        Route::post('/store-update', 'SyncOdoo\ProductPricelistController@store_update');
        Route::delete('/{product_tmpl_id}/{product_uom_id}', 'SyncOdoo\ProductPricelistController@delete');
    });
});

Route::group(['prefix' => 'reset-password'], function(){
    Route::post('/','ResetPasswordController@reset_password');
});
