<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '0');
require_once(base_path() . '/public/ripcord-master/ripcord.php');

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
use App\Jobs\SimpleJob;
use App\Jobs\SyncProductJob;
use App\Models\OdooProductTag;
use Log;
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

    public function try_code(){

        $array_product_tmpl_product = ['25612', '25604'];

        $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'product.template', 'search_read', [[['id', '=', $array_product_tmpl_product]]], ['fields' => ['id','tag_ids']]);

        if(count($product_tag)){
            foreach($product_tag as $item){
                DB::table('odoo_product')->where('product_tmpl_id', $item['id'])->update(['product_tag_id' => $item['tag_ids'][0]]);
            }
        }
    }

    public function sync_product(Request $request){

        $content = request()->file('file');

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

        OdooPricelist::truncate();
        OdooProductTag::truncate();
        OdooProduct::truncate();
        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        // #======PRODUCT TAG==========#
        $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'product.tag', 'search_read', [[['active', '=', True]]], ['fields' => ['id','name']]);

        foreach($product_tag as $item){

            DB::table('odoo_product_tag')->insert([
                'product_tag_id' => $item['id'],
                'name' => $item['name']
            ]);
        }

        #=========PRODUCT===========#

        // $product_id = "37234, 36228, 37235, 37236, 38716, 37232";
        $product_id = file_get_contents($content);
        $product_id = explode(", ",$product_id);
        $chunk = array_chunk($product_id, 10);

        foreach($chunk as $item){
            dispatch(new SyncProductJob($item));
            // dispatch(new SyncProductJob($item))->delay(now()->addSeconds(30));
        }


        return [
            'status' => 200, // SUCCESS
            'message' => 'Export Product Odoo Successfully'
        ];
    }

    public function sync_pelanggan(){
        $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'res.partner', 'search_read', [[['active','=', True]]], ['fields' => [], 'limit'=> 50]);

        try {
            $is_array = is_array($pelanggan) && is_object($pelanggan);
            if($is_array || count($pelanggan)){
                foreach($pelanggan as $item){
                    $user = User::updateOrCreate([
                        'pelanggan_id' => $item['id'],
                        'email' => $item['email'],
                    ],[
                        'type' => 'pelanggan',
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
