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
        
        $sales_order = SalesOrder::where('user_id', auth('api')->user()->id)->with('sales_order_product');

        if($request->has('status')){
            $sales_order = $sales_order->where('status', $request->status);
        };

        return ApiFormatter::success(200, 'Data Sales Order berhasil ditampilkan', $sales_order->get());
    }
}
