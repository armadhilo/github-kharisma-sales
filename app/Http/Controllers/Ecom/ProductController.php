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
        $product = OdooProduct::with('product_tag', 'pricelist')->whereHas('pricelist', function($query){
            $query->where('type', 'b2b');
        })->where('stock', '>', '0')->get();

        $message = ' Data product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }

    public function detail($slug){

        $product = OdooProduct::with('product_tag', 'pricelist')->whereHas('pricelist', function($query){
            $query->where('type', 'b2b');
        })->where('product_name_slug', $slug)->where('stock', '>', '0')->first();
        if(empty($product)){
            $message = 'Data product tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Detail product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }
}
