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
        
        $sales_order = SalesOrder::where('user_id', auth('api')->user()->id)->has('sales_order_product.product')->with('sales_order_product.product');

        if($request->has('status')){
            $sales_order = $sales_order->where('status', $request->status);
        };

        return ApiFormatter::success(200, 'Data Sales Order berhasil ditampilkan', $sales_order->get());
    }

    public function count(){

        $sales_order = SalesOrder::where('user_id', auth('api')->user()->id)->selectRaw('status, count(*) as count')->groupBy('status')->get();
        $data['draft'] = 0;
        $data['sale'] = 0;
        $data['done'] = 0;
        $data['cancel'] = 0;

        foreach($sales_order as $item){
            if($item['status'] == 'draft') $data['draft'] = $item['count'];
            if($item['status'] == 'sale') $data['sale'] = $item['count'];
            if($item['status'] == 'done') $data['done'] = $item['count'];
            if($item['status'] == 'cancel') $data['cancel'] = $item['count'];
        }

        return ApiFormatter::success(200, 'Data Sales Order berhasil ditampilkan', $data);
    }
}
