<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '0');
include(base_path() . '/public/ripcord-master/ripcord.php');

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Kavist\RajaOngkir\Facades\RajaOngkir;

use DB;
use Storage;
use DataTables;
use Carbon\Carbon;

use App\Models\OdooProduct;
use App\Models\OdooPricelist;
use App\Models\User;

use App\Helpers\ApiFormatter;
use App\Models\OdooProductTag;
use ripcord;

class OdooController extends Controller
{

    protected $db;
    protected $username;
    protected $password;
    protected $uid;
    protected $models;

    public function __construct()
    {
        $url = 'http://103.215.177.203:8096';
        $this->db = 'odoo_kharisma_stationery_test5';
        $this->username = "kevin_kevin";
        $this->password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, []);

        $this->models = ripcord::client("$url/xmlrpc/2/object");
    }

    public function sync_product(){

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

        DB::beginTransaction();

        try{

            #======PRODUCT TAG==========#
            $array_product_tag_id = [];
            $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.tag', 'search_read', [[['active', '=', True]]], ['fields' => ['id','name']]);

            foreach($product_tag as $item){
                $array_product_tag_id[] = $item['id'];

                OdooProductTag::updateOrCreate([
                    'product_tag_id' => $item['id']
                ],[
                    'product_tag_id' => $item['id'],
                    'name' => $item['name']
                ]);
            }

            #=========PRODUCT===========#

            $product = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.product', 'search_read', [[]], ['fields' => ['id','product_tmpl_id','barcode','name', 'image', 'image_medium', 'image_small', 'uom_id', 'weight', 'qty_available'], 'limit'=> 50]);

            $array_product_tmpl_product = [];
            foreach($product as $item){

                $array_product_tmpl_product[] = $item['product_tmpl_id'][0];

                OdooProduct::updateOrCreate([
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                ],[
                    'product_id' => $item['id'],
                    'product_tmpl_id' => $item['product_tmpl_id'][0],
                    'sku' => $item['barcode'],
                    'product_name' => $item['name'],
                    'product_name_slug' => Str::slug($item['name']),
                    'image' => $item['image_medium'] == false ? null : $item['image_medium'],
                    'image_small' => $item['image_small'] == false ? null : $item['image_small'],
                    'unit' => $item['uom_id'][1],
                    'weight' => $item['weight'],
                    'stock' => $item['qty_available'],
                ]);
            }

            // get product pricelist for b2c
            $pricelist_b2c = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 1], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity'], 'limit'=> 50]);

            $array_product_tmpl_b2c = [];
            foreach($pricelist_b2c as $item){

                $array_product_tmpl_b2c[] = $item['product_tmpl_id'][0];

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
            'product.pricelist.item', 'search_read', [[['pricelist_id', '=', 7], ['product_tmpl_id', '=', $array_product_tmpl_product]]], ['fields' => ['product_tmpl_id','fixed_price','min_quantity'], 'limit'=> 50]);

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
            'product.template', 'search_read', [[['id', '=', $array_product_tmpl_product]]], ['fields' => ['id','tag_ids'], 'limit'=> 50]);
            foreach($product_tag as $item){
                OdooProduct::where('product_tmpl_id', $item['id'])->update(['product_tag_id' => $item['tag_ids'][0]]);
            }

            // delete product_tag
            OdooProductTag::whereNotIn('product_tag_id', $array_product_tag_id)->delete();

            // delete price product for b2b dan b2c
            OdooPricelist::where(['type' => 'b2c'])->whereNotIn('product_tmpl_id', $array_product_tmpl_b2c)->delete();
            OdooPricelist::where(['type' => 'b2b'])->whereNotIn('product_tmpl_id', $array_product_tmpl_b2b)->delete();

            // delete product
            OdooProduct::whereNotIn('product_tmpl_id', $array_product_tmpl_product)->delete();

            DB::commit();

            return [
                'status' => 200, // SUCCESS
                'message' => 'Export Product Odoo Successfully'
            ];
        }

        catch(\Exception $e){

            DB::rollback();

            return [
                'status' 	=> 300, // GAGAL
                'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
            ];

        }

    }

    public function sync_pelanggan(){
        $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'res.partner', 'search_read', [[['active','=', True]]], ['fields' => [], 'limit'=> 50]);

        try {
            foreach($pelanggan as $item){
                $user = User::updateOrCreate([
                    'pelanggan_id' => $item['id'],
                    'email' => $item['email'],
                ],[
                    'type' => '2',
                    'pelanggan_id' => $item['id'],
                    'email' => $item['email'] == false ? null : $item['email'],
                    'nama' => trim($item['name']),
                    'telepon' => $item['mobile'],
                    'nama_toko' => trim($item['display_name']),
                    'alamat_toko' => $item['street'] . ($item['street2'] != false ? ' - ' . $item['street2'] : '') . ($item['city'] != false ? ' - ' . $item['city'] : '') . ($item['zip'] != false ? ' - ' . $item['zip'] : ''),
                ]);
    
                if($user->wasRecentlyCreated){
                    User::where('id', $user->id)->update([
                        'password' => Hash::make('pelanggan')
                    ]);
                }
            }

            return [
                'status' => 200, // SUCCESS
                'message' => 'Export Pelanggan Odoo Successfully'
            ];
        }
        
        catch(\Exception $e){

            DB::rollback();

            return [
                'status' 	=> 300, // GAGAL
                'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
            ];

        }
    }
}
