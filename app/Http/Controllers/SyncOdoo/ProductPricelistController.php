<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductPricelistRequest;

class ProductPricelistController extends Controller
{
    public function store_update(ProductPricelistRequest $request){
        DB::table('odoo_product_pricelist')->updateOrInsert([
            'product_tmpl_id' => $request->product_tmpl_id,
            'product_uom_id' => $request->product_uom_id
        ], $request->all());
        return ApiFormatter::success(200, 'Sync Product Uom Berhasil');
    }

    public function delete($product_tmpl_id, $product_uom_id){
        DB::table('odoo_product_pricelist')->where([
            'product_tmpl_id' => $product_tmpl_id,
            'product_uom_id' => $product_uom_id
        ])->delete();
        return ApiFormatter::success(200, 'Product Uom Berhasil Dihapus');
    }
}
