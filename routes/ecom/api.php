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
    Route::post('/forgot-password', 'Ecom\AuthController@forgot_password');

    Route::group(['prefix' => 'data-master'], function(){
        Route::get('/check-price','Ecom\DataMasterController@checking_price');
    });

    Route::group(['prefix' => 'product'], function(){
        Route::get('/', 'Ecom\ProductController@index');
        Route::get('/{id}', 'Ecom\ProductController@detail');
    });

    Route::group(['prefix' => 'category'], function(){
        Route::get('/', 'Ecom\CategoryController@index');
    });

    #==================== HARUS LOGIN =====================#

    Route::group(['middleware' => 'validate_login'], function(){

        Route::post('/logout', 'Ecom\AuthController@logout');

        Route::group(['prefix' => 'notification'], function(){
            Route::get('/', 'Ecom\NotificationController@index');
            Route::patch('/mark-all-read', 'Ecom\NotificationController@mark_all_read');
            Route::patch('/mark-one-read/{id}', 'Ecom\NotificationController@mark_one_read');
        });

        Route::group(['prefix' => 'alamat-pengiriman'], function(){
            Route::get('/', 'Ecom\AlamatPengirimanController@index');
            Route::get('/{id}', 'Ecom\AlamatPengirimanController@detail');
            Route::post('/', 'Ecom\AlamatPengirimanController@store_update');
            Route::delete('/{id}', 'Ecom\AlamatPengirimanController@delete');

            Route::get('/setting-alamat-utama/{id}', 'Ecom\AlamatPengirimanController@setting_alamat_utama');
        });

        Route::group(['prefix' => 'profile'], function(){
            Route::get('/', 'Ecom\UserController@detail');
            Route::put('/', 'Ecom\UserController@update');
        });

        Route::group(['prefix' => 'kode-voucher'], function(){
            Route::get('/', 'Ecom\KodeVoucherController@index');
            Route::get('/{id}', 'Ecom\KodeVoucherController@detail');
            Route::post('/claim', 'Ecom\KodeVoucherController@claim');
        });

        Route::group(['prefix' => 'cart'], function(){
            Route::get('/', 'Ecom\CartController@index');
            Route::get('/{uuid}', 'Ecom\CartController@detail');
            Route::post('/', 'Ecom\CartController@store');
            Route::put('/{uuid}', 'Ecom\CartController@update');
            Route::delete('/{uuid}', 'Ecom\CartController@delete');
        });

        Route::group(['prefix' => 'checkout'], function(){
            Route::get('/', 'Ecom\CheckoutController@index');
            Route::post('/', 'Ecom\CheckoutController@checkout');
        });

        Route::group(['prefix' => 'buy-now'], function(){
            Route::get('/', 'Ecom\BuyNowController@index');
            Route::post('/', 'Ecom\BuyNowController@buy_now');
        });

        Route::group(['prefix' => 'wishlist'], function(){
            Route::get('/', 'Ecom\WishlistController@index');
            Route::post('/', 'Ecom\WishlistController@store');
            Route::delete('/', 'Ecom\WishlistController@delete');
        });

        Route::group(['prefix' => 'ongkos-kirim'], function(){
            Route::get('/', 'Ecom\OngkosKirimController@index');
        });
    });
});

