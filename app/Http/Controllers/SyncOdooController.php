<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '0');
require_once(base_path() . '/public/ripcord-master/ripcord.php');

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;

use App\Http\Requests\SyncOdoo\ProductRequest;

use App\Helpers\ApiFormatter;
use App\Http\Requests\SalesOrderRequest;
use App\Jobs\SyncPelangganJob;
use App\Jobs\SyncProductJob;
use App\Models\Cart;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Notification;
use App\Models\OdooProduct;
use ripcord;
use Storage;

class SyncOdooController extends Controller
{

    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $models;

    public function __construct()
    {
        $url = 'http://103.215.177.203:8096';
        $this->db = 'odoo_kharisma_stationery_test6';
        $this->username = "kevin_kevin";
        $this->password = "kevin234";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        $this->models = ripcord::client("$url/xmlrpc/2/object");
    }

    public function try_code(){

        // $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'res.partner', 'search_read', [[['active','=', True]]], ['fields' => [], 'limit'=> 1]);

        // dd($pelanggan);

        //GET CUSTOMER
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'res.partner', 'search_read', [[['active','=', True]]], ['fields' => ['id','email','name','mobile','display_name','street','street2','city','zip'], 'limit'=> 100]);

        //GET PRODUCT
        return $this->models->execute_kw($this->db, $this->uid, $this->password,
        'product.product', 'search_read', [[['product_tmpl_id', '=', [36368]]]], ['fields' => [], 'limit'=> 1]);

        //PRICELIST
        return $this->models->execute_kw($this->db, $this->uid, $this->password,
        'product.pricelist.item', 'search_read', [[['product_tmpl_id', '=', [37374]]]], ['fields' => ['product_tmpl_id', 'min_quantity', 'pricelist_id', 'fixed_price', 'uom_id'], 'limit'=> 10]);

        //GET PRODUCT TAG
        // $array_product_tmpl_product = ['37374'];
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        //     'product.template', 'search_read', [[['id','=',$array_product_tmpl_product]]], ['fields' => [], 'limit' => 1]);

        //GET STATE UNTUK UPDATE SO STATUS
        // $array = ['64842'];
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'sale.order', 'search_read', [[['id','=', $array]]], ['fields' => ['state'], 'limit'=> 1]);

        // $models->execute_kw($db, $uid, $password,
        //     'res.partner', 'check_access_rights',
        //     array('read'), array('raise_exception' => false));

        # search -> akan return ID
        // $models->execute_kw($db, $uid, $password,
        // 'product.product', 'search', [[['write_uid', '=', 1]]]);

        #read -> akan return DETAIL
        // return $models->execute_kw($db, $uid, $password,
        // 'product.product', 'read', [[36228]]);

        // #read -> akan return DETAIL
        // return $models->execute_kw($db, $uid, $password,
        // 'product.product', 'search_read', [[['write_uid', '=', 1]]], ['fields' => ['default_code','active','product_tmpl_id'], 'limit'=> 5]);

        //CREATE CUSTOMER DI SO
        // $partnerData = array(
        //     array('name' => "Partner 1"),
        //     array('name' => "Partner 2"),
        //     array('name' => "Partner 3"),
        // );

        // $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'res.partner', 'create', [$partnerData]);

        //CREATE SO
        // $partnerData = [
        //     [
        //         'partner_id' => 37368,
        //         'order_line' => [
        //             [
        //                 0,
        //                 0,
        //                 [
        //                     'product_id' => 36228, //integer
        //                     'product_uom' => 1103,
        //                     'product_uom_qty' => 2,
        //                     'price_unit' => 4000,
        //                     'tax_id' => [[4,9]],
        //                     'name' => '2TAPE 12x12Y 573912 ALLIGATOR',
        //                 ],
        //             ],
        //             [
        //                 0,
        //                 0,
        //                 [
        //                     'product_id' => 37235, //integer
        //                     'product_uom' => 1580,
        //                     'product_uom_qty' => 2,
        //                     'price_unit' => 5000,
        //                     'tax_id' => [[4,9]],
        //                     'name' => '2TAPE 12x12Y 573912 ALLIGATOR',
        //                 ],
        //             ],
        //             [
        //                 0,
        //                 0,
        //                 [
        //                     'product_id' => 38839, //ONGKOS PENGIRIMAN
        //                     'product_uom' => 1,
        //                     'product_uom_qty' => 1,
        //                     'price_unit' => 5000, //custom
        //                     'name' => 'JNE - HEMAT', //custom
        //                 ],
        //             ]
        //         ],
        //     ],
        // ];

        // $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'sale.order', 'create', [$partnerData]);

        // return $product_tag;
    }

    #PRODUCT
    public function store_update_product(ProductRequest $request){

        if($request->has('product_name')){
            $request->merge(['product_name_slug' => Str::slug($request->product_name)]);
        }

        if($request->has('default_code')){
            $request->merge(['sku' => $request->default_code]);
            $request->request->remove('default_code');
        }

        if($request->has('qty_available')){
            $request->merge(['stock' => $request->qty_available]);
            $request->request->remove('qty_available');
        }

        if($request->has('image_medium')){
            $request->merge(['image_small' => $request->image_medium]);
            $request->request->remove('image_medium');
        }

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

        DB::table('odoo_product')->updateOrInsert(['product_id' => $request->product_id], $request->all());

        return ApiFormatter::success(200, 'Sync Product Berhasil');

    }

    public function delete_product($product_id){

        $odoo_product = DB::table('odoo_product')->where('product_id', $product_id)->first();

        // DELETE AWS
        Storage::disk('s3')->delete('product/images/' . $odoo_product->gd_image_name);
        Storage::disk('s3')->delete('product/images-small/' . $odoo_product->gd_image_small_name);

        DB::table('odoo_product')->where('product_id', $product_id)->delete();

        return ApiFormatter::success(200, 'Product Berhasil Dihapus');
    }
}
