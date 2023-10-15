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

            Route::get('/jadikan-sales/{id}','BackOffice\PelangganController@jadikanSales');
            Route::get('/lock-lokasi/{id}','BackOffice\PelangganController@lockLokasi');
            Route::post('/upload-excel','OdooController@uploadExcelPelanggan');
        });

        Route::prefix('produk')->group(function () {
            Route::get('/','BackOffice\ProdukController@index');
            Route::get('/datatables','BackOffice\ProdukController@datatables');
            Route::post('/sync-produk','BackOffice\ProdukController@sync_produk');
        });

        Route::prefix('user')->group(function () {
            Route::get('/','BackOffice\UserController@index');
            Route::get('/detail/{id}','BackOffice\UserController@detail');
            Route::get('/datatables','BackOffice\UserController@datatables');
            Route::post('/store-update','BackOffice\UserController@store_update');
            Route::delete('/delete/{id}','BackOffice\UserController@delete');
        });

        Route::prefix('edit-absensi-sales')->group(function () {
            Route::get('/','BackOffice\SalesAttendanceController@edit');
            Route::post('/update','BackOffice\SalesAttendanceController@update');
            Route::get('/detail/{id}','BackOffice\SalesAttendanceController@detail');
            Route::get('/datatables','BackOffice\SalesAttendanceController@datatables');
        });

        Route::prefix('edit-kunjungan-sales')->group(function () {
            Route::get('/','BackOffice\KunjunganSalesController@edit');
            Route::get('/datatables','BackOffice\KunjunganSalesController@datatables_edit');
            Route::get('/detail/{id}','BackOffice\KunjunganSalesController@detail');
            Route::post('/update','BackOffice\KunjunganSalesController@update');
        });

        Route::prefix('absensi-sales')->group(function () {
            Route::get('/','BackOffice\SalesAttendanceController@index');
            Route::get('/datatables','BackOffice\SalesAttendanceController@datatables');
        });

        Route::prefix('kunjungan-sales')->group(function () {
            Route::get('/','BackOffice\KunjunganSalesController@index');
            Route::get('/datatables','BackOffice\KunjunganSalesController@datatables');
            Route::get('/datatables/detail','BackOffice\KunjunganSalesController@datatables_detail');
        });

        Route::prefix('sales-log')->group(function () {
            Route::get('/','BackOffice\SalesLogController@index');
            Route::get('/detail/{id}','BackOffice\SalesLogController@detail');
            Route::get('/detail-data/{id}','BackOffice\SalesLogController@detail_data');
            Route::get('/detail-datatables/{id}','BackOffice\SalesLogController@detail_datatables');
            Route::get('/datatables','BackOffice\SalesLogController@datatables');
        });

        Route::prefix('kode-voucher')->group(function () {
            Route::get('/','BackOffice\KodeVoucherController@index');
            Route::get('/detail/{id}','BackOffice\KodeVoucherController@detail');
            Route::get('/datatables','BackOffice\KodeVoucherController@datatables');
            Route::post('/store-update','BackOffice\KodeVoucherController@store_update');
            Route::delete('/delete/{id}','BackOffice\KodeVoucherController@delete');

            Route::get('/datatables-history','BackOffice\KodeVoucherController@datatables_history');
        });

        Route::prefix('transaksi')->group(function(){
            Route::get('/', 'BackOffice\TransaksiController@index');
            Route::get('/detail/{uuid}', 'BackOffice\TransaksiController@detail');
            Route::get('/datatables', 'BackOffice\TransaksiController@datatables');
            Route::get('/cetak-invoice-pdf/{uuid}', 'BackOffice\TransaksiController@cetak_invoice_pdf');
            Route::get('/cetak-resi-pengiriman/{id}', 'BackOffice\TransaksiController@cetak_resi_pengiriman');
        });

        Route::prefix('jasa-pengiriman')->group(function () {
            Route::get('/','BackOffice\JasaPengirimanController@index');
            Route::get('/datatables','BackOffice\JasaPengirimanController@datatables');

            Route::get('/action/{id}/{status}','BackOffice\JasaPengirimanController@action');

            Route::post('/store-update', 'BackOffice\JasaPengirimanController@store_update');
        });

        Route::prefix('config')->group(function () {
            Route::get('/','BackOffice\ConfigController@index');
            Route::get('/detail/{id}','BackOffice\ConfigController@detail');
            Route::get('/datatables','BackOffice\ConfigController@datatables');

            Route::post('/store-update','BackOffice\ConfigController@store_update');
        });

    });
});




