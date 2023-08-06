<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductUomRequest;

class ProductUomController extends Controller
{
    public function store_update(ProductUomRequest $request){

        $request->merge(['created_at' => Carbon::now()]);
        $request->merge(['updated_at' => Carbon::now()]);

        $product_uom = DB::table('odoo_product_uom')->where('product_uom_id', $request->product_uom_id)->first();
        if(empty($product_uom)){
            DB::table('odoo_product_uom')->insert([
                'product_tmpl_id' => '00000',
                'product_uom_id' => $request->product_uom_id,
                'label' => $request->label,
                'stock' => $request->stock,
                'type' => $request->type,
            ]);
        }else{
            DB::table('odoo_product_uom')->where('product_uom_id', $request->product_uom_id)
                ->update($request->all());
        }

        return ApiFormatter::success(200, 'Sync Product Uom Berhasil');
    }

    public function delete($product_uom_id){
        DB::table('odoo_product_uom')->where([
            'product_uom_id' => $product_uom_id
        ])->delete();

        DB::table('odoo_product_pricelist')->where([
            'product_uom_id' => $product_uom_id
        ])->delete();

        return ApiFormatter::success(200, 'Product Uom Berhasil Dihapus');
    }
}
