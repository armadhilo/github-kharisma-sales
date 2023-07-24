<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiFormatter;
use App\Models\OdooProductPricelist;

class DataMasterController extends Controller
{
    public function checking_price(Request $request){
        $validator = Validator::make($request->all(), [
            'product_tmpl_id' => 'required',
            'product_uom_id' => 'required',
            'quantity' => 'required',
        ]);

        if($validator->fails()) {
            return ApiFormatter::error(400, $validator->errors()->first());
        }

        $odoo_pricelist = OdooProductPricelist::where(['product_tmpl_id' => $request->product_tmpl_id, 'product_uom_id' => $request->product_uom_id])
            ->where('min_quantity','<=', $request->quantity)->orderBy('min_quantity', 'desc')->first();

        $data['price'] = $odoo_pricelist->price;
        $data['total_price'] = $request->quantity * $data['price'];

        return ApiFormatter::success(200, 'Check price berhasil dilakukan', $data);
    }
}
