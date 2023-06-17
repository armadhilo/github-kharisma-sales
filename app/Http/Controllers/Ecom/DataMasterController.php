<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\WishlistRequest;
use App\Models\OdooProduct;
use App\Models\Wishlist;

use App\Http\Requests\PelangganRequest;
use App\Models\OdooPricelist;

class DataMasterController extends Controller
{
    public function check_price(Request $request){
        $validator = Validator::make($request->all(), [
            'product_tmpl_id' => 'required',
            'quantity' => 'required',
        ]);
    
        if($validator->fails()) {
            return ApiFormatter::error(400, $validator->errors()->first());
        }

        $type = 'b2c';
        if(auth('api')->check()){
            $type = 'b2b';
        }

        $type = 'b2b';

        $odoo_pricelist = OdooPricelist::where(['type' => $type, 'product_tmpl_id' => $request->product_tmpl_id])
            ->where('min_quantity','<=', $request->quantity)->orderBy('min_quantity', 'desc')->first();

        $data['price'] = $odoo_pricelist->price;
        $data['total_price'] = $request->quantity * $data['price'];

        return ApiFormatter::success(200, 'Check price berhasil dilakukan', $data);
    }
}
