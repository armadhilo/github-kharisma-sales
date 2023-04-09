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
    });
});




