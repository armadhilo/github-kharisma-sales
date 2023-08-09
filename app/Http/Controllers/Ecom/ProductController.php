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
    public function index(Request $request)
    {
        $product = OdooProduct::with('product_tag', 'product_uom.product_pricelist')->orderBy('updated_at','DESC')->limit(10);

        if($request->has('search') && !empty($request->search)){
            $search = str_replace("+"," ",$request->search);
            $product->where('product_name','like',"%$search%");
        }

        if($request->has('kategori') && !empty($request->kategori) && $request->kategori != 'All Category'){
            $product->where('array_product_tag_id', 'like', '%' . $request->kategori . '%');
        }

        $product = $product->get();

        if($request->has('filter') && !empty($request->filter)){
            $filter = $request->filter;
            if($filter == 'termurah'){
                $product = $product->sortBy(function($product){
                    return $product->price_utama;
                });
            }elseif($filter == 'termahal'){
                $product = $product->sortByDesc(function($product){
                    return $product->price_utama;
                });
            }else{
                $product = $product->sortByDesc('updated_at');
            }

            $product = $product->all();
            $indexedData = array_values($product);

            $product = $indexedData;
        }

        $message = ' Data product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }

    public function detail($slug)
    {
        $product = OdooProduct::with('product_tag', 'product_uom.product_pricelist')->where('product_name_slug', $slug)->first();
        if(empty($product)){
            $message = 'Data product tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Detail product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }
}
