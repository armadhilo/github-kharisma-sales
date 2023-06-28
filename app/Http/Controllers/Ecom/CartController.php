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

class CartController extends Controller
{
    public function index(Request $request){

        $cart = Cart::with('product.product_tag')->active();

        if($request->has('type') && $request->type == 'count'){
            $message = 'Data jumlah cart berhasil ditampilkan';
            $cart = $cart->count();
        }else{
            $message = 'Data cart berhasil ditampilkan';
            $cart = $cart->get();
            if(count($cart) == 0) $message = 'Data cart list empty';
        }

        return ApiFormatter::success(200, $message, $cart);
    }

    public function detail($uuid){

        $cart = Cart::with('product.product_tag')->active()->where(['uuid' => $uuid])->first();
        if(empty($cart)){
            $message = 'Cart not found';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Cart loaded successfully';
        return ApiFormatter::success(200, $message, $cart);
    }

    public function store(CartRequest $request){

        $request->merge(['uuid' => Str::uuid() ]);
        $request->merge(['user_id' => auth('api')->user()->id ]);

        //recheck price
        $odoo_product = OdooProduct::where('product_id', $request->product_id)->first();
        if(empty($odoo_product)){
            $message = 'Oops! product tidak ditemukan, silahkan pilih product lainnya';
            return ApiFormatter::error(400, $message);
        }

        $recheck_price = $this->check_price($odoo_product->product_tmpl_id, $request->quantity);
        $request->merge(['price' => $recheck_price['price'] ]);

        //jika barang yang dibeli kembar jadikan 1 cart
        $cart = Cart::active()->where(['product_id' => $request->product_id])->first();
        if(!empty($cart)) {
            $request->merge(['quantity' => $cart->quantity + $request->quantity ]);
            $request->merge(['uuid' => $cart->uuid ]);
        }

        //check stock barang
        $cek_stock = $this->check_stock($request->product_id, $request->quantity);
        if(!$cek_stock){
            $message = 'Oops! stock tidak cukup';
            return ApiFormatter::error(400, $message);
        }

        $request->merge(['total_price' => $request->price * $request->quantity ]);

        Cart::updateOrCreate(['uuid' => $request->uuid], $request->all());

        $message = 'Cart created successfully';
        return ApiFormatter::success(200, $message);
    }

    public function update($uuid, CartRequest $request){

        $cart = Cart::where(['uuid' => $uuid])->first();
        if(empty($cart)){
            $message = 'Cart not found';
            return ApiFormatter::error(404, $message);
        }

        //check stock barang
        $cek_stock = $this->check_stock($request->product_id, $request->quantity);
        if(!$cek_stock){
            $message = 'Oops! stock tidak cukup';
            return ApiFormatter::success(400, $message);
        }

         //recheck price
         $odoo_product = OdooProduct::where('product_id', $request->product_id)->first();
         $recheck_price = $this->check_price($odoo_product->product_tmpl_id, $request->quantity);
         $request->merge(['price' => $recheck_price['price'] ]);

        $request->merge(['total_price' => $request->price * $request->quantity ]);

        $cart->fill($request->all())->save();
        $message = 'Cart Updated Successfully';
        return ApiFormatter::success(200, $message);
    }

    public function delete($uuid){

        $cart = Cart::where(['user_id' => auth('api')->user()->id, 'uuid' => $uuid])->first();

        if(empty($cart)){
            $message = 'Cart not found';
            return ApiFormatter::error(404, $message);
        }

        $cart->delete();
        $message = 'Cart Deleted Successfully';
        return ApiFormatter::success(200, $message);

    }
}
