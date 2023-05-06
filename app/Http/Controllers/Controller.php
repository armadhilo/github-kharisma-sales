<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Pagination\CustomPagination;

use App\Models\OdooProduct;

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
}
