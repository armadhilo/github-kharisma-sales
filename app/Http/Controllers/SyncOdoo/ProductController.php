<?php

namespace App\Http\Controllers\SyncOdoo;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use Carbon\Carbon;
use DB;
use Storage;

use App\Http\Requests\SyncOdoo\ProductRequest;
use App\Models\OdooProductOriginal;

class ProductController extends Controller
{
    public function store_update(ProductRequest $request){

        if($request->has('image')){
            //save image on google drive
            // Base64 encoded string
            $base64String = $request->image;
            // Decode the Base64 string
            $decodedData = base64_decode($base64String);
            // Create a Fileinfo resource
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            // Get the MIME type of the decoded data
            $mimeType = explode('/', finfo_buffer($finfo, $decodedData))[1];
            // Close the Fileinfo resource
            finfo_close($finfo);

            $filename = $request->sku .'-'. 'big.' . $mimeType;

            //DELETE
            Storage::disk('s3')->delete('product/images/' . $filename);

            //array insert product
            $request->merge(['gd_image_name' => $filename]);
            $request->merge(['gd_image_mimetype' => $mimeType]);

            Storage::disk('s3')->put('product/images/' . $filename, base64_decode($request->image));
            $url_image = 'https://'. env('AWS_BUCKET') .'.s3.'. env('AWS_DEFAULT_REGION') .'.amazonaws.com/product/images/' . $filename;

            $request->merge(['gd_image_name' => $url_image]);

            $request->request->remove('image');
        }

        if($request->has('image_small')){
            //save image on google drive
            // Base64 encoded string
            $base64String = $request->image_small;
            // Decode the Base64 string
            $decodedData = base64_decode($base64String);
            // Create a Fileinfo resource
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            // Get the MIME type of the decoded data
            $mimeType_small = explode('/', finfo_buffer($finfo, $decodedData))[1];
            // Close the Fileinfo resource
            finfo_close($finfo);

            $filename_small = $request->sku .'-'. 'medium.' . $mimeType_small;

            //DELETE
            Storage::disk('s3')->delete('product/images-small/' . $filename_small);

            //array insert product
            $request->merge(['gd_image_small_name' => $filename_small]);
            $request->merge(['gd_image_small_mimetype' => $mimeType_small]);

            Storage::disk('s3')->put('product/images-small/' . $filename_small, base64_decode($request->image_small));
            $url_image_small = 'https://'. env('AWS_BUCKET') .'.s3.'. env('AWS_DEFAULT_REGION') .'.amazonaws.com/product/images-small/' . $filename_small;
            $request->merge(['gd_image_small_path' => $url_image_small]);

            $request->request->remove('image_small');
        }

        if($request->product_tag_id){

            $product_tag_id = json_decode($request->product_tag_id, true);

            if(!is_array($product_tag_id)){
                return ApiFormatter::error(400, 'Product Tag ID harus berupa array');
            }

            $array = [];
            foreach($product_tag_id as $item){
                $array[] = (int) $item;
            }

            $request->merge(['array_product_tag_id' => json_encode($array)]);
            $request->merge(['product_tag_id' => $request->product_tag_id[0]]);
        }

        OdooProductOriginal::updateOrInsert([
            'product_id' => $request->product_id,
            'sku' => $request->sku,
        ], $request->all());

        return ApiFormatter::success(200, 'Sync Product Berhasil');

    }

    public function delete($product_id){

        $odoo_product = OdooProductOriginal::where('product_id', $product_id)->first();

        if(empty($odoo_product)){
            return ApiFormatter::error(404, 'Product Not Found');
        }

        // DELETE AWS
        Storage::disk('s3')->delete('product/images/' . $odoo_product->gd_image_name);
        Storage::disk('s3')->delete('product/images-small/' . $odoo_product->gd_image_small_name);

        OdooProductOriginal::where('product_id', $product_id)->delete();

        return ApiFormatter::success(200, 'Product Berhasil Dihapus');
    }
}
