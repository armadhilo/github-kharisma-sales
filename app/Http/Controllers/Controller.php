<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Helpers\ApiFormatter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Pagination\CustomPagination;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\OdooProduct;
use App\Models\OdooPricelist;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new CustomPagination(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }

    public function check_stock($product_id, $quantity){
        $stock = OdooProduct::where(['product_id' => $product_id])->first()->stock;
        if($stock < $quantity){
            return false;
        }

        return true;
    }

    public function check_price($product_tmpl_id, $quantity){
        $type = 'b2c';
        if(auth('api')->check()){
            $type = 'b2b';
        }

        $odoo_pricelist = OdooPricelist::where(['type' => $type, 'product_tmpl_id' => $product_tmpl_id])
            ->where('min_quantity','<=', $quantity)->orderBy('min_quantity', 'desc')->first();

        if(empty($odoo_pricelist)){
            $odoo_pricelist = OdooPricelist::with('product')->where(['type' => $type, 'product_tmpl_id' => $product_tmpl_id])->orderBy('min_quantity', 'desc')->first();
            throw new HttpResponseException(ApiFormatter::error(400, 'Minimal quantity untuk produk ' . $odoo_pricelist->product->product_name . ' adalah ' . $odoo_pricelist->min_quantity .' '. $odoo_pricelist->product->unit));
        }

        $data['price'] = $odoo_pricelist->price;
        $data['total_price'] = $quantity * $data['price'];

        return $data;
    }
}
