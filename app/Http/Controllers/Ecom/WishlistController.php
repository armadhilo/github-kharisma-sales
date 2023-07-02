<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\WishlistRequest;
use App\Models\OdooProduct;
use App\Models\Wishlist;

use App\Http\Requests\PelangganRequest;

class WishlistController extends Controller
{
    public function index(Request $request){
        $type = auth('api')->check() ? 'b2b' : 'b2c';
        $product = OdooProduct::with('product_tag', 'pricelist', 'wishlist')->whereHas('pricelist', function($query) use ($type){
            $query->where('type', $type);
        })->whereHas('wishlist', function($query){
            $query->where('user_id', auth('api')->user()->id);
        })->where('stock', '>', '0');

        $message = 'Data wishlist berhasil ditampilkan';

        if($request->has('type') && $request->type == 'count'){
            $message = 'Data jumlah wishlist berhasil ditampilkan';
            $product = $product->count();
        }else{
            $product = $product->get();
            if(count($product) == 0) $message = 'Data wishlist list empty';
        }

        return ApiFormatter::success(200, $message, $product);
    }

    public function store(WishlistRequest $request){

        $user_id = auth('api')->user()->id;
        $wishlist = Wishlist::where([
            'product_id' => $request->product_id,
            'user_id' => $user_id,
        ])->first();

        if(!empty($wishlist)){
            $message = 'Product sudah menjadi wishlist';
            return ApiFormatter::error(400, $message);
        }

        Wishlist::insert([
            'product_id' => $request->product_id,
            'user_id' => $user_id,
        ]);

        $message = 'Wishlist berhasil ditambahkan';
        return ApiFormatter::success(200, $message);
    }

    public function delete(WishlistRequest $request){

        Wishlist::where([
            'product_id' => $request->product_id,
            'user_id' => auth('api')->user()->id,
        ])->delete();

        $message = 'Wishlist berhasil dihapus';
        return ApiFormatter::success(200, $message);
    }
}
