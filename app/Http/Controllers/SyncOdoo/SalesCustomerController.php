<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\SalesCustomerRequest;
use App\Models\SalesPelanggan;

class SalesCustomerController extends Controller
{
    public function store_update(SalesCustomerRequest $request){
        dd($request->all());
        SalesPelanggan::updateOrInsert(['sales_id' => $request->sales_id, 'pelanggan_id' => $request->pelanggan_id], $request->all());
        return ApiFormatter::success(200, 'Sync Sales Pelanggan Berhasil');
    }

    public function delete($sales_id, $pelanggan_id){
        SalesPelanggan::where(['sales_id' => $sales_id, 'pelanggan_id' => $pelanggan_id])->delete();
        return ApiFormatter::success(200, 'Sales Pelanggan Berhasil Dihapus');
    }
}
