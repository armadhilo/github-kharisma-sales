<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Models\OdooProduct;
use App\Models\User;

class ProductController extends Controller
{
    public function index(){

        $type = auth('api')->check() ? 'b2b' : 'b2c';

        $product = OdooProduct::with('product_tag', 'pricelist')->whereHas('pricelist', function($query) use ($type){
            $query->where('type', $type);
        })->where('stock', '>', '0')->get();

        $message = ' Data product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }

    public function detail($slug){

        $type = auth('api')->check() ? 'b2b' : 'b2c';

        $product = OdooProduct::with('product_tag', 'pricelist')->whereHas('pricelist', function($query) use ($type){
            $query->where('type', $type);
        })->where('product_name_slug', $slug)->where('stock', '>', '0')->first();
        if(empty($product)){
            $message = 'Data product tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Detail product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }
}
