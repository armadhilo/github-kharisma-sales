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
use App\Jobs\SyncPelangganJob;
use App\Jobs\SyncProductJob;
use App\Models\Cart;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Notification;
use App\Models\OdooProduct;
use App\Models\SalesPelanggan;
use ripcord;
use Storage;
use Validator;
use App\Imports\CsvDataImport;
use Excel;

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

        // TOTAL PRODUCT
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.product', 'search', [[['active','=', True]]]);

        //GET CUSTOMER
        return $this->models->execute_kw($this->db, $this->uid, $this->password,
        'res.partner', 'search_read', [[['id', '=', [80575]]]], ['fields' => [], 'limit'=> 1]);

        // //GET PRODUCT
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.product', 'search_read', [[['product_tmpl_id', '=', [36368]]]], ['fields' => [], 'limit'=> 1]);

        // //PRICELIST
        // return $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.pricelist.item', 'search_read', [[['product_tmpl_id', '=', [37374]]]], ['fields' => ['product_tmpl_id', 'min_quantity', 'pricelist_id', 'fixed_price', 'uom_id'], 'limit'=> 10]);

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

            //get sales_id
            $sales = null;
            $sales_pelanggan = SalesPelanggan::where('pelanggan_id', auth('api')->user()->pelanggan_id)->select('sales_id')->first();
            if(!empty($sales_pelanggan)) $sales = $sales_pelanggan->sales_id;

            //create SALES_ORDER table
            $sales_order = SalesOrder::create([
                'uuid' => Str::uuid(),
                'user_id' => auth('api')->user()->pelanggan_id,
                'sales_id' => $sales,
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
                $cek_stock = $this->check_stock($item->product_tmpl_id, $item->product_uom_id, $item->quantity);
                if(!$cek_stock){
                    //delete barang yang stocknya habis dari cart
                    $array_product_no_stock[] = $item;
                }

                //recheck price
                $recheck_price = $this->check_price($item->product_tmpl_id, $item->product_uom_id, $item->quantity);
                $total_harga_produk += $recheck_price['price'] * $item->quantity;

                //siapkan parameter order line
                $array_order_line[] = [
                    0,
                    0,
                    [
                        'product_id' => $item->product_id, //integer
                        'product_uom' => $item->product_uom_id,
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
                    'product_id' => $item->product_id,
                    'product_tmpl_id' => $item->product_tmpl_id,
                    'product_uom_id' => $item->product_uom_id,
                    'quantity' => $item->quantity,
                    'harga' => $recheck_price['price']
                ]);
            }

            //add ongkos kirim ke dalam order line
            // $array_order_line[] = [
            //     0,
            //     0,
            //     [
            //         'product_id' => 38839, //ONGKOS PENGIRIMAN
            //         'product_uom' => 1,
            //         'product_uom_qty' => 1,
            //         'price_unit' => $request->ongkos_kirim, //custom
            //         'name' => $request->jasa_pengiriman, //custom
            //     ],
            // ];

            //jika ada product yang tidak punya stock dihapus dari cart
            if(count($array_product_no_stock)){

                Cart::active()->whereIn(['product_id', $array_product_no_stock])->delete();

                $message = 'Oops! beberapa barang sudah habis kami hapus dari keranjangmu ya';
                return ApiFormatter::error(400, $message);
            }

            $salesOrderData = [
                'partner_id' => (int) $cart[0]->user->pelanggan_id,
                'odoo_sales_user_id' => (int) $sales,
                'order_line' => $array_order_line,
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
                'user_id' => auth('api')->user()->pelanggan_id,
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


        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        // #======PRODUCT TAG==========#
        // $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.tag', 'search_read', [[['active', '=', True]]], ['fields' => ['id','name']]);

        // foreach($product_tag as $item){

        //     DB::table('odoo_product_tag')->updateOrInsert(['product_tag_id' => $item['id']],[
        //         'product_tag_id' => $item['id'],
        //         'name' => $item['name'],
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ]);
        // }

        #=========PRODUCT===========#

        //WITH FILE
        // $content = request()->file('file');
        // $product_id = file_get_contents($content);
        // $product_id = explode(", ",$product_id);
        // $chunk = array_chunk($product_id, 10);

        //WITH ODOO GET ID
        $product_id = $this->models->execute_kw($this->db, $this->uid, $this->password,'product.product', 'search', [[['active','=', True]]]);
        $chunk = array_chunk($product_id, 20);

        foreach($chunk as $item){
            dispatch(new SyncProductJob($item, $this->uid));
            // dispatch(new SyncProductJob($item))->delay(now()->addSeconds(30));
        }

        return [
            'status' => 200, // SUCCESS
            'message' => 'Export Product Odoo Successfully'
        ];
    }

    public function sync_product_dummy(Request $request){

        $content = request()->file('file');

        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        // #======PRODUCT TAG==========#
        // $product_tag = $this->models->execute_kw($this->db, $this->uid, $this->password,
        // 'product.tag', 'search_read', [[['active', '=', True]]], ['fields' => ['id','name']]);

        // foreach($product_tag as $item){

        //     DB::table('odoo_product_tag')->updateOrInsert(['product_tag_id' => $item['id']],[
        //         'product_tag_id' => $item['id'],
        //         'name' => $item['name'],
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ]);
        // }

        #=========PRODUCT===========#

        $product_id = [37234, 36228, 37235, 37236, 38716, 37232];
        // $product_id = file_get_contents($content);
        // $product_id = explode(", ",$product_id);
        $chunk = array_chunk($product_id, 10);

        foreach($chunk as $item){
            // dispatch(new SyncProductJob($item));
            try{

                $array_product_tmpl_product = [];

                $product = $this->models->execute_kw($this->db, $this->uid, $this->password,
                    'product.product', 'search_read', [[['id', '=', $item]]], ['fields' => ['id','product_tmpl_id','uom_id','tag_ids','default_code','name', 'image', 'image_medium', 'image_small', 'uom_id', 'weight', 'qty_available']]);

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

                            //delete pricelist yang akan diupdate harganya
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

                                    DB::table('odoo_product_uom')->insert([
                                        'product_tmpl_id' => $item['product_tmpl_id'][0],
                                        'product_uom_id' => $item['uom_id'][0],
                                        'label' => $item['uom_id'][1],
                                        'stock' => $stock,
                                        'type' => $item['pricelist_id'][1],
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now(),
                                    ]);

                                    DB::table('odoo_product_pricelist')->insert([
                                        'product_tmpl_id' => $item['product_tmpl_id'][0],
                                        'product_uom_id' => $item['uom_id'][0],
                                        'price' => $item['fixed_price'],
                                        'min_quantity' => $item['min_quantity'],
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

        return [
            'status' => 200, // SUCCESS
            'message' => 'Export Product Odoo Successfully'
        ];
    }

    public function sync_pelanggan(){

        DB::table('jobs')->truncate();
        DB::table('failed_jobs')->truncate();

        $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'res.partner', 'search', [[['active','=', True]]]);

        $chunk = array_chunk($pelanggan, 20);

        foreach($chunk as $item){
            dispatch(new SyncPelangganJob($item, $this->uid));
        }

        return [
            'status' => 200, // SUCCESS
            'message' => 'Sync Pelanggan Successfully'
        ];
    }

    public function sync_pelanggan_by_id(Request $request){
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required',
        ]);

        if($validator->fails()) {
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        $explode = explode("|", $request->partner_id . '|');
        $array = [];
        foreach($explode as $item){
           if(!empty($item)) $array[] = (int)$item;
        }

        $pelanggan = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'res.partner', 'search_read', [[['id', '=', $array], ['active','=', True]]], ['fields' => ['property_product_pricelist','id','email','name','mobile','display_name','npwp','npwp_name','id_nik','street','street2','city','zip']]);

        if(!count($pelanggan)){
            return [
                'status' => 300, // SUCCESS
                'message' => 'Oops koneksi Odoo sedang error atau ID Partner tidak ditemukan di database ODOO'
            ];
        }

        if(is_array($pelanggan) || count($pelanggan)){
            foreach($pelanggan as $item){

                //jika pelannggan punya nama
                if(!empty($item['name'])){
                    $user = User::updateOrCreate([
                        'pelanggan_id' => $item['id'],
                    ],[
                        'type' => 'pelanggan',
                        'type_pelanggan' => $item['property_product_pricelist'][1],
                        'pelanggan_id' => $item['id'],
                        'email' => $item['email'] == false ? null : $item['email'],
                        'nama' => trim($item['name']),
                        'telepon' => $item['mobile'],
                        'nama_toko' => trim($item['display_name']),
                        'no_npwp' => $item['npwp'],
                        'nama_npwp' => $item['npwp_name'],
                        'no_ktp' => $item['id_nik'],
                        'street' => $item['street'],
                        'street2' => $item['street2'],
                        'city' => $item['city'],
                        'zip' => $item['zip'],
                        'alamat_toko' => $item['street'] . ($item['street2'] != false ? ' - ' . $item['street2'] : '') . ($item['city'] != false ? ' - ' . $item['city'] : '') . ($item['zip'] != false ? ' - ' . $item['zip'] : ''),
                    ]);

                    if($user->wasRecentlyCreated){
                        User::where('id', $user->id)->update([
                            'password' => Hash::make('pelanggan')
                        ]);
                    }
                }
            }
        }

        return [
            'status' => 200, // SUCCESS
            'message' => 'Sync ID Partner berhasil'
        ];
    }

    public function uploadExcelPelanggan(Request $request){
        if($request->hasFile('file_excel')){

			$data = Excel::toCollection(new CsvDataImport,request()->file('file_excel'))[0];

			if($data->count()){

                DB::beginTransaction();

                try{

                    // SalesPelanggan::truncate();

                    $array_sales_id = [];
                    $array_pelanggan_id = [];

                    foreach ($data as $key => $value) {
                        // SalesPelanggan::create([
                        //     "sales_id" => $value['salespartner_id'],
                        //     "pelanggan_id" => $value['customer_id']
                        // ]);

                        $array_sales_id[] = $value['salespartner_id'];
                        $array_pelanggan_id[] = $value['customer_id'];
                    }

                    $array = array_merge($array_sales_id, $array_pelanggan_id);
                    $array = array_unique($array);

                    $users_id = User::pluck('pelanggan_id')->toArray();
                    $missingData = array_diff($array, $users_id);

                    if(count($missingData)){
                        DB::table('jobs')->truncate();
                        DB::table('failed_jobs')->truncate();

                        $chunk = array_chunk($missingData, 20);
                        foreach($chunk as $item){
                            dispatch(new SyncPelangganJob($item, $this->uid));
                        }
                    }

                    DB::commit();

                    return [
                        'status' => 200, // SUCCESS
                        'message' => 'Upload Excel dan Sync Pelanggan Successfully'
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
    }

    public function sync_status(){
        $sales_order = SalesOrder::whereIn('status', ['draft','sale'])->get()->pluck('odoo_order_id');
        $stringCollection = collect($sales_order)->map(function ($item) {
            return (string) $item;
        });
        $stringArray = $stringCollection->all();
        $odoo_api = $this->models->execute_kw($this->db, $this->uid, $this->password,
        'sale.order', 'search_read', [[['id','=', $stringArray]]], ['fields' => ['state']]);

        foreach($odoo_api as $item){
            SalesOrder::where('odoo_order_id', $item['id'])->update(['status' => $item['state']]);
        }

        return [
            'status' => 200, // SUCCESS
            'message' => 'Export Status Sales Order Successfully'
        ];
    }

    private function generate_no_invoice(){
        $tahun = Carbon::now()->year;
        $month = Carbon::now()->format('m');
        $date = Carbon::now()->format('d');
        $count_sales_order = SalesOrder::whereYear('created_at', $tahun)->count();

        return 'INV-' . $tahun .'/'. $date . $month . str_pad($count_sales_order + 1, 6, "0", STR_PAD_LEFT);
    }
}
