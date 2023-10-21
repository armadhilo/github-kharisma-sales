<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductPricelistRequest;
use App\Models\OdooProductOriginal;
use App\Models\OdooProductPricelist;
use App\Models\OdooProductUomOriginal;

class ProductPricelistController extends Controller
{
    public function store_update(ProductPricelistRequest $request){

        $request->merge(['created_at' => Carbon::now()]);
        $request->merge(['updated_at' => Carbon::now()]);

        $product = DB::table('odoo_product')->where('product_tmpl_id', $request->product_tmpl_id)->first();
        if(empty($product)){
            return ApiFormatter::error(400, 'Master Product tidak ditemukan harap daftarkan terlebih dahulu');
        }

        DB::beginTransaction();

        try{

            OdooProductUomOriginal::updateOrInsert([
                'product_tmpl_id' => $request->product_tmpl_id,
                'product_uom_id' => $request->product_uom_id,
                'label' => $request->label,
                'type' => $request->type,
            ],[
                'product_tmpl_id' => $request->product_tmpl_id,
                'product_uom_id' => $request->product_uom_id,
                'label' => $request->label,
                'stock' => $request->stock,
                'type' => $request->type,
                'note' => 'API SYNC PRICELIST [' . Carbon::now()->format('Y-m-d H:i:s') . ']',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            OdooProductPricelist::updateOrInsert([
                'product_tmpl_id' => $request->product_tmpl_id,
                'product_uom_id' => $request->product_uom_id,
                'label' => $request->label,
                'type' => $request->type,
            ],[
                'product_tmpl_id' => $request->product_tmpl_id,
                'product_uom_id' => $request->product_uom_id,
                'price' => $request->price,
                'min_quantity' => $request->min_quantity,
                'label' => $request->label,
                'stock' => $request->stock,
                'type' => $request->type,
                'note' => 'API SYNC PRICELIST [' . Carbon::now()->format('Y-m-d H:i:s') . ']',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return ApiFormatter::success(200, 'Sync Product Pricelist Berhasil');
        }

        catch(\Exception $e){

            DB::rollback();
            return ApiFormatter::error(500, $e->getMessage());
        }
    }

    public function delete($product_tmpl_id, $product_uom_id, $type){

        $type = $type . ' (IDR)';

        OdooProductUomOriginal::where([
            'product_tmpl_id' => $product_tmpl_id,
            'product_uom_id' => $product_uom_id,
            'type' => $type,
        ])->delete();

        OdooProductPricelist::where([
            'product_tmpl_id' => $product_tmpl_id,
            'product_uom_id' => $product_uom_id,
            'type' => $type,
        ])->delete();

        return ApiFormatter::success(200, 'Product Pricelist Berhasil Dihapus');
    }
}
