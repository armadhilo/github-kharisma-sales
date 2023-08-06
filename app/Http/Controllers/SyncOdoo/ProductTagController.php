<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductTagRequest;
use App\Models\OdooProductTag;

class ProductTagController extends Controller
{
    public function store_update(ProductTagRequest $request){
        OdooProductTag::updateOrInsert(['product_tag_id' => $request->product_tag_id], $request->all());
        return ApiFormatter::success(200, 'Sync Product Tag Berhasil');
    }
}
