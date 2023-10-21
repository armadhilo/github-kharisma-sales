<?php

namespace App\Jobs;

require_once(base_path() . '/public/ripcord-master/ripcord.php');

use App\Models\OdooProductPricelist;
use App\Models\OdooProductOriginal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use ripcord;
use Str;
use DB;
use Log;

class SyncProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $models;

    protected $data;

    public $timeout = 120;
    public $tries = 3;

    public function __construct($data, $uid)
    {
        $url = config('services.odoo.odoo_url');
        $this->db = config('services.odoo.odoo_db');
        $this->username = config('services.odoo.odoo_username');
        $this->password = config('services.odoo.odoo_password');

        // $common = ripcord::client("$url/xmlrpc/2/common");
        // $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        $this->models = ripcord::client("$url/xmlrpc/2/object");

        $this->data = $data;
        $this->uid = $uid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try{

            $array_product_tmpl_product = [];

            $product = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'product.product', 'search_read', [[['id', '=', $this->data]]], ['fields' => ['id','product_tmpl_id','uom_id','tag_ids','default_code','name', 'image', 'image_medium', 'image_small', 'uom_id', 'weight', 'qty_available']]);

            if(isset($product['faultCode']) && $product['faultCode'] == 1){
                Log::error($product['faultString']);
                $this->fail(new \Exception($product['faultString']));
            }

            if(is_array($product) && count($product)){
                foreach($product as $item){

                    $array_insert_product = [
                        'product_id' => $item['id'],
                        'product_tmpl_id' => $item['product_tmpl_id'][0],
                        'product_uom_id' => $item['uom_id'][0],
                        'product_tag_id' => $item['tag_ids'][0],
                        'array_product_tag_id' => json_encode([$item['tag_ids'][0]]),
                        'sku' => $item['default_code'],
                        'product_name' => $item['name'],
                        'product_name_slug' => Str::slug($item['name']),
                        'unit' => $item['uom_id'][1],
                        'weight' => $item['weight'],
                        'stock' => $item['qty_available'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];

                    if(is_array($item)){

                        //jika data image tidak kosong
                        if(!empty($item['image'] || $item['image'] != false || $item['image'] != '' )){

                            //save image on google drive
                            // Base64 encoded string
                            $base64String = $item['image'];
                            // Decode the Base64 string
                            $decodedData = base64_decode($base64String);
                            // Create a Fileinfo resource
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            // Get the MIME type of the decoded data
                            $mimeType = explode('/', finfo_buffer($finfo, $decodedData))[1];
                            // Close the Fileinfo resource
                            finfo_close($finfo);

                            $filename = $item['default_code'] .'-'. 'big.' . $mimeType;

                            //DELETE
                            Storage::disk('s3')->delete('product/images/' . $filename);

                            //array insert product
                            $array_insert_product['gd_image_name'] = $filename;
                            $array_insert_product['gd_image_mimetype'] = $mimeType;

                            //check apabila gambar sudah ada tidak perlu menambahkan image baru
                            Storage::disk('s3')->put('product/images/' . $filename, base64_decode($item['image']));
                            $url_image = 'https://'. env('AWS_BUCKET') .'.s3.'. env('AWS_DEFAULT_REGION') .'.amazonaws.com/product/images/' . $filename;

                            $array_insert_product['gd_image_path'] = $url_image;

                        }

                        if(!empty($item['image_medium'])){

                            //save image on google drive
                            // Base64 encoded string
                            $base64String = $item['image_medium'];
                            // Decode the Base64 string
                            $decodedData = base64_decode($base64String);
                            // Create a Fileinfo resource
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            // Get the MIME type of the decoded data
                            $mimeType_small = explode('/', finfo_buffer($finfo, $decodedData))[1];
                            // Close the Fileinfo resource
                            finfo_close($finfo);

                            $filename_small = $item['default_code'] .'-'. 'medium.' . $mimeType_small;

                            //DELETE
                            Storage::disk('s3')->delete('product/images-small/' . $filename_small);

                            //array insert product
                            $array_insert_product['gd_image_small_name'] = $filename_small;
                            $array_insert_product['gd_image_small_mimetype'] = $mimeType_small;

                            //check apabila gambar sudah ada tidak perlu menambahkan image baru
                            Storage::disk('s3')->put('product/images-small/' . $filename_small, base64_decode($item['image_medium']));
                            $url_image_small = 'https://'. env('AWS_BUCKET') .'.s3.'. env('AWS_DEFAULT_REGION') .'.amazonaws.com/product/images-small/' . $filename_small;

                            $array_insert_product['gd_image_small_path'] = $url_image_small;
                        }

                        $array_product_tmpl_product[] = is_array($item['product_tmpl_id']) ? $item['product_tmpl_id'][0] : $item['product_tmpl_id'];

                        DB::table('odoo_product')->updateOrInsert(['product_id' => $item['id']], $array_insert_product);
                    }
                }

                if(count($array_product_tmpl_product)){
                    // get product pricelist for b2c
                    $product_pricelist = $this->models->execute_kw($this->db, $this->uid, $this->password,
                    'product.pricelist.item', 'search_read', [[['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => []]);

                    if(is_array($product_pricelist) && count($product_pricelist)){

                        // delete pricelist yang akan diupdate harganya
                        DB::table('odoo_product_uom')->whereIn('product_tmpl_id', $array_product_tmpl_product)->delete();
                        DB::table('odoo_product_pricelist')->whereIn('product_tmpl_id', $array_product_tmpl_product)->delete();

                        foreach($product_pricelist as $item){

                            if(is_array($item['product_tmpl_id'])){

                                //field stock pada odoo_product_uom
                                $stock = 1;
                                if(is_string($item['uom_id'][1])){
                                    if (str_contains($item['uom_id'][1], '/')) {
                                        $explode_stock = explode("/",$item['uom_id'][1]);
                                        $stock = $explode_stock[1];
                                    }
                                }

                                #PRODUCT UOM
                                DB::table('odoo_product_uom')->insert([
                                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                                    'product_uom_id' => $item['uom_id'][0],
                                    "label" => $item['uom_id'][1],
                                    "stock" => $stock,
                                    "type" => $item['pricelist_id'][1],
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                ]);

                                #PRODUCT PRICELIST
                                DB::table('odoo_product_pricelist')->insert([
                                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                                    'product_uom_id' => $item['uom_id'][0],
                                    "price" => $item['fixed_price'],
                                    "min_quantity" => $item['min_quantity'],
                                    "label" => $item['uom_id'][1],
                                    "stock" => $stock,
                                    "type" => $item['pricelist_id'][1],
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                ]);
                            }
                        }
                    }
                }
            }
        }

        catch(\Exception $e){

            Log::error($e);

            $this->fail(new \Exception($e));
        }
    }
}
