<?php

use App\Jobs\SyncProductJob;
use Illuminate\Support\Facades\Route;
use League\Flysystem\Filesystem;

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

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('reset-password','ResetPasswordController@index');
Route::post('reset-password','ResetPasswordController@set_password');

//Reset Password Success
Route::view('/reset-password-success', 'auth.reset-password-berhasil');


Route::middleware(['cors','cek_login'])->group(function () {

    Route::prefix('change-password')->group(function () {
        Route::get('/','LoginController@change_password');
        Route::post('/action-change-password','LoginController@action_change_password');
    });

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
        });

        Route::prefix('error')->group(function () {
            Route::view('/401', 'errors.404');
            Route::view('/404', 'errors.404');
            Route::view('/under-maintenance', 'errors.under-maintenance');
        });

    });
});

#==================== TEST GOOGLE DRIVE ========================#

Route::get('test', function() {
    return Storage::disk('google')->put('test2.txt', 'Hello World');
});

Route::get('list-folder-contents', function() {

    $dir = '/';
    $recursive = false; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));

    return $contents->where('type', '=', 'file'); // directories
    $filteredCollection = $contents->where('type', '=', 'file')->whereIn('filename', ['180900076-med']); // files
    return $filteredCollection->all();
});

Route::get('get', function() {
    //https://drive.google.com/uc?export=view&id=1G3217hFYoDG551VX1CVIhYQN0kP_zNCC
    $filename = "coba.jpg";

    $dir = '/';
    $recursive = true; // Get subdirectories also?
    $contents = collect(Storage::cloud()->listContents($dir, $recursive));

    $file = $contents
        ->where('type', '=', 'file')
        ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
        ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
        ->first(); // there can be duplicate file names!

    //return $file; // array with file info

    $rawData = Storage::cloud()->get($file['path']);

    return response($rawData, 200)
        ->header('ContentType', $file['mimetype'])
        ->header('Content-Disposition', "attachment; filename='$filename'");
});




