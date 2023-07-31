<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\PelangganRequest;

class PelangganController extends Controller
{
    public function store_update(PelangganRequest $request){
        DB::table('users')->updateOrInsert(['pelanggan_id' => $request->pelanggan_id], $request->all());
        return ApiFormatter::success(200, 'Sync Pelanggan Berhasil');
    }

    public function delete($pelanggan_id){
        DB::table('users')->where('pelanggan_id', $pelanggan_id)->delete();
        return ApiFormatter::success(200, 'Pelanggan Berhasil Dihapus');
    }
}
