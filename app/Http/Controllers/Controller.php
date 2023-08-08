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
use App\Models\OdooProductPricelist;
use App\Models\OdooProductUom;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new CustomPagination(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }

    private function check_stock($product_tmpl_id, $product_uom_id, $quantity){

        $product_uom = OdooProductUom::where(['product_tmpl_id' => $product_tmpl_id, 'product_uom_id' => $product_uom_id])->first();
        $product_uom_stock = $product_uom->stock * $quantity;

        $stock = OdooProduct::where(['product_tmpl_id' => $product_tmpl_id])->first()->stock;
        if($stock < $product_uom_stock){
            return false;
        }

        return true;
    }

    private function check_price($product_tmpl_id, $product_uom_id, $quantity){
        $odoo_pricelist = OdooProductPricelist::where('min_quantity','<=', $quantity)->where(['product_tmpl_id' => $product_tmpl_id, 'product_uom_id' => $product_uom_id])->orderBy('min_quantity', 'desc')->first();

        if(empty($odoo_pricelist)){
            $odoo_pricelist = OdooProductPricelist::with('product', 'product_uom')->where(['product_tmpl_id' => $product_tmpl_id, 'product_uom_id' => $product_uom_id])->orderBy('min_quantity', 'desc')->first();
            throw new HttpResponseException(ApiFormatter::error(400, 'Minimal quantity satuan '. $odoo_pricelist->product_uom->label .' untuk produk ' . $odoo_pricelist->product->product_name . ' adalah ' . $odoo_pricelist->min_quantity));
        }

        $data['price'] = $odoo_pricelist->price;
        $data['total_price'] = $quantity * $data['price'];

        return $data;
    }
}
