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
        $product = OdooProduct::with('product_tag', 'pricelist')->orderBy('updated_at','DESC');

        if($request->has('search')){
            $search = str_replace("+"," ",$request->search);
            $product->where('product_name','like',"%$search%");
        }

        if($request->has('kategori')){
            $product->where('product_tag_id', $request->kategori);
        }

        $product = $product->get();

        if($request->has('filter')){
            $filter = $request->filter;
            if($filter == 'termurah'){
                $product = $product->sortBy(function($product){
                    return $product->pricelist[0]->price;
                });
            }elseif($filter == 'termahal'){
                $product = $product->sortByDesc(function($product){
                    return $product->pricelist[0]->price;
                });
            }else{
                $product = $product->sortByDesc('updated_at');
            }
        }

        $message = ' Data product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }

    public function detail($slug)
    {
        $product = OdooProduct::with('product_tag', 'pricelist')->where('product_name_slug', $slug)->first();
        if(empty($product)){
            $message = 'Data product tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Detail product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }
}
