<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Models\SalesOrder;
use App\Models\User;

class SalesOrderController extends Controller
{
    public function index(Request $request){

        $sales_order = SalesOrder::active()->with('sales_order_product.product', 'sales_order_product.product_uom.product_pricelist')->has('sales_order_product.product');

        if($request->has('status')){
            $sales_order = $sales_order->where('status', $request->status);
        };

        return ApiFormatter::success(200, 'Data Sales Order berhasil ditampilkan', $sales_order->get());
    }

    public function detail($sales_order_uuid){

        $sales_order = SalesOrder::with('sales_order_product.product', 'sales_order_product.product_uom.product_pricelist')->has('sales_order_product.product')->where('uuid', $sales_order_uuid)->first();

        return ApiFormatter::success(200, 'Data Sales Order detail berhasil ditampilkan', $sales_order);
    }

    public function count(){

        $data['draft'] = "0";
        $data['sale'] = "0";
        $data['done'] = "0";
        $data['cancel'] = "0";

        $sales_order = SalesOrder::active()->selectRaw('status, count(*) as count')->has('sales_order_product.product')->groupBy('status')->get();
        if(count($sales_order)){
            foreach($sales_order as $item){
                if($item['status'] == 'draft') $data['draft'] = (string)$item['count'];
                if($item['status'] == 'sale') $data['sale'] = (string)$item['count'];
                if($item['status'] == 'done') $data['done'] = (string)$item['count'];
                if($item['status'] == 'cancel') $data['cancel'] = (string)$item['count'];
            }
        }

        return ApiFormatter::success(200, 'Data Sales Order berhasil ditampilkan', $data);
    }
}
