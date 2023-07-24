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
use App\Http\Requests\Ecom\CartRequest;
use App\Models\Cart;
use App\Models\OdooProduct;

class BuyNowController extends Controller
{

    public function index(){
        $checkout = Cart::with('product.product_tag')->buyNow()->limit(1)->get();

        $message = 'Data buy now berhasil ditampilkan';
        if(empty($checkout)){
            $message = 'Data item buy now not found';
        }

        return ApiFormatter::success(200, $message, $checkout);
    }

    public function buy_now(CartRequest $request){
        DB::beginTransaction();
        try{

            //check barang
            $odoo_product = OdooProduct::where('product_id', $request->product_id)->first();
            if(empty($odoo_product)){
                $message = 'Oops! product tidak ditemukan, silahkan pilih product lainnya';
                return ApiFormatter::error(400, $message);
            }

            //check stock barang
            $cek_stock = $this->check_stock($request->product_tmpl_id, $request->product_uom_id, $request->quantity);
            if(!$cek_stock){
                $message = 'Oops! stock tidak cukup';
                return ApiFormatter::error(400, $message);
            }

            //recheck price
            $recheck_price = $this->check_price($request->product_tmpl_id, $request->product_uom_id, $request->quantity);
            $request->merge(['price' => $recheck_price['price'] ]);

            //delete cart buy now
            Cart::buyNow()->delete();

            //merge is_buy_now = 1
            $request->merge(['is_buy_now' => 1 ]);
            $request->merge(['uuid' => Str::uuid() ]);
            $request->merge(['user_id' => auth('api')->user()->id ]);
            $request->merge(['total_price' => $request->price * $request->quantity ]);

            Cart::create($request->all());
            DB::commit();

            $message = ' Data product buy now berhasil ditambahkan';
            return ApiFormatter::success(200, $message, $request->uuid);

        }

        catch(\Exception $e){

            DB::rollback();
            return ApiFormatter::error(500, $e->getMessage());
        }
    }
}
