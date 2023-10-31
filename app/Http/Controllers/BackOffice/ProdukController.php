<?php

namespace App\Http\Controllers\BackOffice;

ini_set('max_execution_time', '0');

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\OdooProductOriginal;
use DataTables;
use Illuminate\Http\Request;
use DB;

class ProdukController extends Controller
{
    public function index(){

        $data['jobs'] = DB::table('jobs')->count();
        $data['failed_jobs'] = DB::table('failed_jobs')->count();
        $data['total_product'] = OdooProductOriginal::count();

        return view('admin.produk.index', $data);
    }

    public function datatables(){
        $product = OdooProductOriginal::with('product_tag', 'product_uom.product_pricelist')->get();

        return DataTables::of($product)
                ->addIndexColumn()
                // ->addColumn('custom_price', function($item) {
                //     $custom_price = '';

                //     if(count($item->product_uom)){
                //         foreach($item->product_uom as $uom){
                //             $custom_price .= '<b>' . $uom->type .' - '. $uom->label . '</b><br>';

                //             if(count($uom->product_pricelist)){
                //                 foreach($uom->product_pricelist as $pricelist){
                //                     $custom_price .= 'quantity > ' . $pricelist->min_quantity . ' price ' . number_format($pricelist->price,0) . '<br>';
                //                 }
                //             }
                //         }
                //     }

                //     return $custom_price;
                // })
                // ->rawColumns(['custom_price'])
                ->make(true);
    }
}
