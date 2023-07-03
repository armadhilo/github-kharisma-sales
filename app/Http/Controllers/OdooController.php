<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '0');
require_once(base_path() . '/public/ripcord-master/ripcord.php');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;

use App\Models\User;

use App\Helpers\ApiFormatter;
use App\Http\Requests\SalesOrderRequest;
use App\Jobs\SyncProductJob;
use App\Models\Cart;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Notification;
use App\Models\OdooProduct;
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

        $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'res.partner', 'search_read', [[['active','=', True]]], ['fields' => [], 'limit'=> 1]);

        dd($pelanggan);

        //GET PRODUCT
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.product', 'search_read', [[]], ['fields' => [], 'limit'=> 1]);

        //GET PRODUCT TAG
        // $array_product_tmpl_product = ['37374'];
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        //     'product.template', 'search_read', [[['id','=',$array_product_tmpl_product]]], ['fields' => [], 'limit' => 1]);

        //GET STATE UNTUK UPDATE SO STATUS
        // $array = ['64842'];
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'sale.order', 'search_read', [[['id','=', $array]]], ['fields' => ['state'], 'limit'=> 1]);

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

    public function sales_order(SalesOrderRequest $request){

        DB::beginTransaction();

        try{

            $array_product_no_stock = [];
            $array_order_line = [];
            $array_cart_id = [];
            $total_harga_produk = 0;

            $cart = Cart::with('product','user')->whereIn('uuid', $request->cart_id)->get();

            if(!count($cart)){
                $message = 'Oops! Produk tidak ditemukan';
                return ApiFormatter::error(400, $message);
            }

            //create SALES_ORDER table
            $sales_order = SalesOrder::create([
                'uuid' => Str::uuid(),
                'user_id' => auth('api')->user()->id,
                'sales_id' => session('sales_id'),
                'no_invoice' => $this->generate_no_invoice(),
                'total_berat' => $request->total_berat,
                'ongkos_kirim' => $request->ongkos_kirim,
                'jasa_pengiriman' => $request->jasa_pengiriman,
            ]);

            $array_product = [];

            foreach($cart as $key => $item){

                $array_cart_id[] = $item->id;
                $array_product[$item->product->id] = $item->quantity;

                //check stock barang
                $cek_stock = $this->check_stock($item->product->product_id, $item->quantity);
                if(!$cek_stock){
                    //delete barang yang stocknya habis dari cart
                    $array_product_no_stock[] = $item;
                }

                //recheck price
                $recheck_price = $this->check_price($item->product->product_tmpl_id, $item->quantity);
                $total_harga_produk += $recheck_price['price'] * $item->quantity;

                //siapkan parameter order line
                $array_order_line[] = [
                    0,
                    0,
                    [
                        'product_id' => $item->product->product_id, //integer
                        'product_uom' => $item->product->product_uom_id,
                        'product_uom_qty' => $item->quantity,
                        'price_unit' => $recheck_price['price'],
                        'tax_id' => [[4,9]],
                        'name' => $item->product->product_name,
                    ],
                ];

                //insert into SALES_ORDER_PRODUCT table
                SalesOrderProduct::create([
                    'sales_order_id' => $sales_order->id,
                    'cart_id' => $item->id,
                    'product_id' => $item->product->product_id,
                    'quantity' => $item->quantity,
                    'harga' => $recheck_price['price']
                ]);
            }

            //add ongkos kirim ke dalam order line
            $array_order_line[] = [
                0,
                0,
                [
                    'product_id' => 38839, //ONGKOS PENGIRIMAN
                    'product_uom' => 1,
                    'product_uom_qty' => 1,
                    'price_unit' => $request->ongkos_kirim, //custom
                    'name' => $request->jasa_pengiriman, //custom
                ],
            ];

            //jika ada product yang tidak punya stock dihapus dari cart
            if(count($array_product_no_stock)){

                Cart::active()->whereIn(['product_id', $array_product_no_stock])->delete();

                $message = 'Oops! beberapa barang sudah habis kami hapus dari keranjangmu ya';
                return ApiFormatter::error(400, $message);
            }

            $salesOrderData = [
                'partner_id' => (int) $cart[0]->user->pelanggan_id,
                'order_line' => [
                    $array_order_line
                ],
            ];

            $sales_order_odoo = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'sale.order', 'create', [[$salesOrderData]]);

            if(isset($sales_order_odoo['faultCode'])){
                $message = 'Oops! terjadi kesalahan waktu membuat sales order odoo';
                return ApiFormatter::error(400, $message);
            }

            //kurangi stock pada ODOO PRODUCT TABLE
            foreach($array_product as $key => $item){
                $odoo_product = OdooProduct::find($key);

                $old_stock = $odoo_product->stock;

                $odoo_product->stock = (int)$old_stock - (int)$item;
                $odoo_product->save();
            }

            //update data SALES_ORDER TABLE
            SalesOrder::where('id', $sales_order->id)->update([
                'odoo_order_id' => $sales_order_odoo[0],
                'total_harga_produk' => $total_harga_produk,
                'total_harga' => $total_harga_produk + $request->ongkos_kirim,
            ]);

            //update status on CART table
            Cart::whereIn('id', $array_cart_id)->update([
                'status' => 1,
            ]);

            //simpan notification untuk order
            Notification::create([
                'user_id' => auth('api')->user()->id,
                'sales_order_id' => $sales_order->id,
                'notification_type' => 'draft',
            ]);

            DB::commit();

            return ApiFormatter::success(200, 'Sales Order berhasil dibuat');

        }

        catch(\Exception $e){

            DB::rollback();
            return ApiFormatter::error(500, $e->getMessage());
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

        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        // #======PRODUCT TAG==========#
        $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'product.tag', 'search_read', [[['active', '=', True]]], ['fields' => ['id','name']]);

        foreach($product_tag as $item){

            DB::table('odoo_product_tag')->updateOrInsert(['product_tag_id' => $item['id']],[
                'product_tag_id' => $item['id'],
                'name' => $item['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
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

    private function generate_no_invoice(){
        $tahun = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $date = Carbon::now()->format('d');
        $count_sales_order = SalesOrder::whereYear('created_at', $tahun)->count();

        return 'INV-' . $tahun .'/'. $date . $month . str_pad($count_sales_order + 1, 6, "0", STR_PAD_LEFT);
    }
}
