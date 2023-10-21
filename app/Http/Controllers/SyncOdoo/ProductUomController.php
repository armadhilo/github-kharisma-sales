<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductUomRequest;
use App\Models\OdooProductPricelist;
use App\Models\OdooProductUom;

class ProductUomController extends Controller
{
    public function store_update(ProductUomRequest $request){

        $request->merge(['created_at' => Carbon::now()]);
        $request->merge(['updated_at' => Carbon::now()]);

        DB::beginTransaction();

        try{

            OdooProductUom::where(['product_uom_id' => $request->product_uom_id])->update([
                'label' => $request->label,
                'stock' => $request->stock,
                'note' => 'API SYNC UOM [' . Carbon::now()->format('Y-m-d H:i:s') . ']'
            ]);

            OdooProductPricelist::where(['product_uom_id' => $request->product_uom_id])->update([
                'label' => $request->label,
                'stock' => $request->stock,
                'note' => 'API SYNC UOM [' . Carbon::now()->format('Y-m-d H:i:s') . ']'
            ]);

            DB::commit();

            return ApiFormatter::success(200, 'Sync Product Uom Berhasil');
        }

        catch(\Exception $e){

            DB::rollback();
            return ApiFormatter::error(500, $e->getMessage());
        }
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
