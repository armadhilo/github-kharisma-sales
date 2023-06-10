<?php

namespace App\Jobs;

require_once(base_path() . '/public/ripcord-master/ripcord.php');

use App\Models\OdooPricelist;
use App\Models\OdooProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
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

    public function __construct($data)
    {
        $url = 'http://103.215.177.203:8096';
        $this->db = 'odoo_kharisma_stationery_test5';
        $this->username = "kevin_kevin";
        $this->password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        $this->models = ripcord::client("$url/xmlrpc/2/object");

        $this->data = $data;
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
            $array_filename = [];

            $product = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'product.product', 'search_read', [[['id', '=', $this->data],['qty_available','>',0]]], ['fields' => ['id','product_tmpl_id','barcode','name', 'image', 'image_medium', 'image_small', 'uom_id', 'weight', 'qty_available']]);

            if(empty($product)){
                Log::error('Fetch product odoo empty result');
                $this->fail(new \Exception('Fetch product odoo empty result'));
            }

            if(isset($product['faultCode']) && $product['faultCode'] == 1){
                Log::error($product['faultString']);
                $this->fail(new \Exception($product['faultString']));
            }

            if(count($product)){
                foreach($product as $item){

                    if(is_array($item)){
                        $filename = null;
                        $mimeType = null;
                        $filename_small = null;
                        $mimeType_small = null;

                        //jika data image tidak kosong
                        if(!empty($item['image'])){

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

                            $filename = $item['barcode'] .'-'. 'big.' . $mimeType;
                            $array_filename[] = $filename;

                            Storage::disk('google')->put($filename, base64_decode($item['image']));
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

                            $filename_small = $item['barcode'] .'-'. 'medium.' . $mimeType_small;
                            $array_filename[] = $filename_small;

                            Storage::disk('google')->put($filename_small, base64_decode($item['image_medium']));
                        }

                        $array_product_tmpl_product[] = is_array($item['product_tmpl_id']) ? $item['product_tmpl_id'][0] : $item['product_tmpl_id'];

                        DB::table('odoo_product')->insert([
                            'product_id' => $item['id'],
                            'product_tmpl_id' => $item['product_tmpl_id'][0],
                            'sku' => $item['barcode'],
                            'product_name' => $item['name'],
                            'product_name_slug' => Str::slug($item['name']),
                            'gd_image_name' => $filename,
                            'gd_image_mimetype' => $mimeType,
                            'gd_image_small_name' => $filename_small,
                            'gd_image_small_mimetype' => $mimeType_small,
                            'unit' => $item['uom_id'][1],
                            'weight' => $item['weight'],
                            'stock' => $item['qty_available'],
                        ]);
                    }
                }

                $dir = '/';
                $recursive = false; // Get subdirectories also?
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));

                //return $contents->where('type', '=', 'dir'); // directories
                $data = $contents->where('type', '=', 'file')->whereIn('name', $array_filename); // files
                $data = $data->unique('name')->all();

                if(count($data)){
                    foreach($data as $item){
                        DB::table('odoo_product')->where('gd_image_name', $item['name'])->update([
                            'gd_image_path' => 'https://drive.google.com/uc?export=view&id=' . $item['path']
                        ]);

                        DB::table('odoo_product')->where('gd_image_small_name', $item['name'])->update([
                            'gd_image_small_path' => 'https://drive.google.com/uc?export=view&id=' . $item['path']
                        ]);
                    }
                }

                if(count($array_product_tmpl_product)){
                    // get product pricelist for b2c
                    $pricelist_b2c = $this->models->execute_kw($this->db, $this->uid, $this->password,
                    'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 1], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity']]);
                    if(count($pricelist_b2c)){
                        foreach($pricelist_b2c as $item){
                            DB::table('odoo_pricelist')->insert([
                                'product_tmpl_id' => $item['product_tmpl_id'][0],
                                'price' => $item['fixed_price'],
                                'min_quantity' => $item['min_quantity'],
                                'type' => 'b2c',
                            ]);
                        }
                    }


                    // get product pricelist for b2b
                    $pricelist_b2b = $this->models->execute_kw($this->db, $this->uid, $this->password,
                    'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 7], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity']]);
                    if(count($pricelist_b2b)){
                        foreach($pricelist_b2b as $item){
                            DB::table('odoo_pricelist')->insert([
                                'product_tmpl_id' => $item['product_tmpl_id'][0],
                                'price' => $item['fixed_price'],
                                'min_quantity' => $item['min_quantity'],
                                'type' => 'b2b',
                            ]);
                        }
                    }

                    //relational product and product_tag
                    $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
                    'product.template', 'search_read', [[['id', '=', $array_product_tmpl_product]]], ['fields' => ['id','tag_ids']]);
                    if(count($product_tag)){
                        foreach($product_tag as $item){
                            DB::table('odoo_product')->where('product_tmpl_id', $item['id'])->update(['product_tag_id' => $item['tag_ids'][0]]);
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
