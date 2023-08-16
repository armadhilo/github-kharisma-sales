<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\SalesOrderRequest;

class SalesOrderController extends Controller
{
    public function update(SalesOrderRequest $request){

        $request->merge(['updated_at' => Carbon::now()]);

        $sales_order = DB::table('sales_order')->where('odoo_order_id', $request->odoo_order_id)->first();
        if(empty($sales_order)){
            return ApiFormatter::success(404, 'Sales Order tidak ditemukan pada sistem');
        }

        DB::table('sales_order')->where('odoo_order_id', $request->odoo_order_id)->update(['status' => $request->status]);
        return ApiFormatter::success(200, 'Sync Product Uom Berhasil');
    }
}
