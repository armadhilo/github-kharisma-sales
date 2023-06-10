<?php

namespace App\Http\Controllers\BackOffice;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\OdooProduct;
use App\Models\OdooProductOriginal;
use Illuminate\Http\Request;
use DB;

class ProdukController extends Controller
{
    public function index(){

        $data['jobs'] = DB::table('jobs')->count();
        $data['failed_jobs'] = DB::table('failed_jobs')->count();
        $data['total_product'] = OdooProduct::count();

        return view('admin.produk.index', $data);
    }

    public function datatables(){
        $product = OdooProductOriginal::with('pricelist')->get();

        $message = ' Data product berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $product);
    }
}
