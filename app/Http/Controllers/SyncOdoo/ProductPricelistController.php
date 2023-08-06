<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductPricelistRequest;
use App\Models\OdooProductPricelist;

class ProductPricelistController extends Controller
{
    public function store_update(ProductPricelistRequest $request){

        $request->merge(['created_at' => Carbon::now()]);
        $request->merge(['updated_at' => Carbon::now()]);

        $product_uom_check = DB::table('odoo_product_uom')->where('product_uom_id', $request->product_uom_id)->first();
        if(empty($product_uom_check)){
            return ApiFormatter::success(400, 'Master Uom tidak ditemukan harap daftarkan terlebih dahulu');
        }

        $product_uom = DB::table('odoo_product_uom')->where([
                'product_uom_id' => $request->product_uom_id,
                'product_tmpl_id' => $request->product_tmpl_id,
                'type'=> $request->type,
            ])
            ->first();

        if(empty($product_uom)){
            DB::table('odoo_product_uom')->insert([
                'product_tmpl_id' => $request->product_tmpl_id,
                'product_uom_id' => $request->product_uom_id,
                'label' => $product_uom_check->label,
                'stock' => $product_uom_check->stock,
                'type' => $request->type,
            ]);
        }

        OdooProductPricelist::updateOrInsert([
            'product_tmpl_id' => $request->product_tmpl_id,
            'product_uom_id' => $request->product_uom_id
        ], $request->all());
        return ApiFormatter::success(200, 'Sync Product Pricelist Berhasil');
    }

    public function delete($product_tmpl_id, $product_uom_id){
        OdooProductPricelist::where([
            'product_tmpl_id' => $product_tmpl_id,
            'product_uom_id' => $product_uom_id
        ])->delete();
        return ApiFormatter::success(200, 'Product Pricelist Berhasil Dihapus');
    }
}
