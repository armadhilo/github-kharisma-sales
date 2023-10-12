<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\PelangganRequest;
use App\Models\SalesPelanggan;
use App\Models\User;
use Hash;

class PelangganController extends Controller
{
    public function store_update(PelangganRequest $request){

        $user = User::where('pelanggan_id', $request->pelanggan_id)->first();
        if(empty($user)){
            $request->merge(['type' => 'pelanggan']);
            $request->merge(['password' => Hash::make('pelanggan')]);
        }

        if(isset($request->sales_id)){
            SalesPelanggan::updateOrInsert(['pelanggan_id' => $request->pelanggan_id], [
                'pelanggan_id' => $request->pelanggan_id,
                'sales_id' => $request->sales_id
            ]);
        }

        User::updateOrInsert(['pelanggan_id' => $request->pelanggan_id], $request->all());
        return ApiFormatter::success(200, 'Sync Pelanggan Berhasil');
    }

    public function delete($pelanggan_id){
        User::where('pelanggan_id', $pelanggan_id)->delete();
        return ApiFormatter::success(200, 'Pelanggan Berhasil Dihapus');
    }
}
