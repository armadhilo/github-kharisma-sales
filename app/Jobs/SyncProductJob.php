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
use ripcord;
use Str;
use DB;
use Storage;
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

        DB::beginTransaction();

        try{

            $array_product_tmpl_product = [];
            $array_filename = [];

            $product = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'product.product', 'search_read', [[['id', '=', $this->data]]], ['fields' => ['id','product_tmpl_id','barcode','name', 'image', 'image_medium', 'image_small', 'uom_id', 'weight', 'qty_available']]);

            if(empty($product)){
                Log::error('Oops! something wrong with odoo server');
            }

            if(isset($product['faultCode']) && $product['faultCode'] == 1){
                Log::error($product['faultString']);
            }

            foreach($product as $item){

                if(is_array($item)){
                    $filename = '';
                    $mimeType = '';
                    $filename_small = '';
                    $mimeType_small = '';

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

                    OdooProduct::updateOrCreate([
                        'product_tmpl_id' => $item['product_tmpl_id'][0],
                    ],[
                        'gd_name' => $filename,
                        'gd_mimetype' => $mimeType,
                        'product_id' => $item['id'],
                        'product_tmpl_id' => $item['product_tmpl_id'][0],
                        'sku' => $item['barcode'],
                        'product_name' => $item['name'],
                        'product_name_slug' => Str::slug($item['name']),
                        // 'image' => $item['image'] == false ? null : $item['image'],
                        'gd_image_name' => $filename == false ? null : $filename,
                        'gd_image_mimetype' => $mimeType == false ? null : $mimeType,
                        // 'image_small' => $item['image_small'] == false ? null : $item['image_small'],
                        'gd_image_small_name' => $filename_small == false ? null : $filename_small,
                        'gd_image_small_mimetype' => $mimeType_small == false ? null : $mimeType_small,
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
            $data = $data->all();

            foreach($data as $item){
                OdooProduct::where('gd_image_name', $item['name'])->update([
                    'gd_image_path' => 'https://drive.google.com/uc?export=view&id=' . $item['path']
                ]);

                OdooProduct::where('gd_image_small_name', $item['name'])->update([
                    'gd_image_small_path' => 'https://drive.google.com/uc?export=view&id=' . $item['path']
                ]);
            }

            // get product pricelist for b2c
            $pricelist_b2c = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 1], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity']]);

            foreach($pricelist_b2c as $item){

                OdooPricelist::updateOrCreate([
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                ], [
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                    'price' => $item['fixed_price'],
                    'min_quantity' => $item['min_quantity'],
                    'type' => 'b2c',
                ]);
            }

            // get product pricelist for b2b
            $pricelist_b2c = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 7], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity']]);

            $array_product_tmpl_b2b = [];
            foreach($pricelist_b2c as $item){
                $array_product_tmpl_b2b[] = $item['product_tmpl_id'][0];

                OdooPricelist::updateOrCreate([
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                ], [
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                    'price' => $item['fixed_price'],
                    'min_quantity' => $item['min_quantity'],
                    'type' => 'b2b',
                ]);
            }

            //relational product and product_tag
            $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.template', 'search_read', [[['id', '=', $array_product_tmpl_product]]], ['fields' => ['id','tag_ids']]);
            foreach($product_tag as $item){
                OdooProduct::where('product_tmpl_id', $item['id'])->update(['product_tag_id' => $item['tag_ids'][0]]);
            }

            DB::commit();
        }

        catch(\Exception $e){

            DB::rollback();

            $this->failed($e);

        }
    }

    public function failed($exception)
    {
        Log::error($exception->getMessage());
    }
}
