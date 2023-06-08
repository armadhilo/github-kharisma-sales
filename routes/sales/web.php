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

Route::middleware(['cors','cek_login'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::prefix('sales')->group(function () {
            Route::get('/','BackOffice\SalesController@index');
            Route::get('/detail/{id}','BackOffice\SalesController@detail');
            Route::get('/datatables','BackOffice\SalesController@datatables');
            Route::post('/store-update','BackOffice\SalesController@store_update');
            Route::delete('/delete/{id}','BackOffice\SalesController@delete');
        });

        Route::prefix('pelanggan')->group(function () {
            Route::get('/','BackOffice\PelangganController@index');
            Route::get('/add','BackOffice\PelangganController@add');
            Route::get('/detail/{id}','BackOffice\PelangganController@detail');
            Route::get('/datatables','BackOffice\PelangganController@datatables');
            Route::post('/store-update','BackOffice\PelangganController@store_update');
            Route::put('/store-update','BackOffice\PelangganController@store_update');
            Route::delete('/delete/{id}','BackOffice\PelangganController@delete');
        });

        Route::prefix('produk')->group(function () {
            Route::get('/','BackOffice\ProdukController@index');
            Route::get('/datatables','BackOffice\ProdukController@datatables');
            Route::post('/sync-produk','BackOffice\ProdukController@sync_produk');
        });

        Route::prefix('kunjungan-sales')->group(function () {
            Route::get('/','BackOffice\KunjunganSalesController@index');
            Route::get('/detail/{id}','BackOffice\KunjunganSalesController@detail');
            Route::get('/datatables','BackOffice\KunjunganSalesController@datatables');
        });

        Route::prefix('kode-voucher')->group(function () {
            Route::get('/','BackOffice\KodeVoucherController@index');
            Route::get('/detail/{id}','BackOffice\KodeVoucherController@detail');
            Route::get('/datatables','BackOffice\KodeVoucherController@datatables');
            Route::post('/store-update','BackOffice\KodeVoucherController@store_update');
            Route::delete('/delete/{id}','BackOffice\KodeVoucherController@delete');

            Route::get('/datatables-history','BackOffice\KodeVoucherController@datatables_history');
        });

<<<<<<< HEAD
=======
        Route::prefix('produk')->group(function () {
            Route::get('/','BackOffice\ProdukController@index');
            Route::get('/datatables','BackOffice\ProdukController@datatables');
        });
>>>>>>> 45e00f258bfe5ef0cd6d22e33b3d8500962b00fa

        Route::prefix('jasa-pengiriman')->group(function () {
            Route::get('/','BackOffice\JasaPengirimanController@index');
            Route::get('/datatables','BackOffice\JasaPengirimanController@datatables');

            Route::get('/action/{id}/{status}','BackOffice\JasaPengirimanController@action');

            Route::post('/store-update', 'BackOffice\JasaPengirimanController@store_update');
        });
    });
});




