<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use DB;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\CheckoutRequest;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function index(){
        $checkout = Cart::with('product.product_tag')->where(['user_id' => auth('api')->user()->id, 'is_checkout' => 1])->get();

        $message = 'Data checkout berhasil ditampilkan';
        if(count($checkout) == 0) $message = 'Data checkout list empty';

        return ApiFormatter::success(200, $message, $checkout);
    }

    public function checkout(CheckoutRequest $request){

        DB::beginTransaction();
        try{

            Cart::active()->update(['is_checkout' => 0]);
            Cart::active()->whereIn('uuid', $request->product_id)->update(['is_checkout' => 1]);

            DB::commit();

            $message = ' Data checkout berhasil ditambahkan';
            return ApiFormatter::success(200, $message);

        }

        catch(\Exception $e){

            DB::rollback();
            return ApiFormatter::error(500, $e);
        }
    }
}
