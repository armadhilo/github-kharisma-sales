<?php

namespace App\Http\Controllers;

include(base_path() . '/public/ripcord-master/ripcord.php');

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kavist\RajaOngkir\Facades\RajaOngkir;

use Storage;
use DataTables;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Kunjungan;
use App\Models\Config;

use App\Helpers\ApiFormatter;
use ripcord;

class DataMasterCOntroller extends Controller
{

    #============== TRY CODE ====================#

    public function try_code(){
        $url = 'http://103.215.177.203:8096';
        $db = 'odoo_kharisma_stationery_test4';
        $username = "kevin_kevin";
        $password = "kevin123";

        $common = ripcord::client("$url/xmlrpc/2/common");
        $uid = $common->authenticate($db, $username, $password, []);

        $models = ripcord::client("$url/xmlrpc/2/object");
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

        return $models->execute_kw($db, $uid, $password,
        'product.product', 'search_read', [[['pricelist_id', '=', '7']]], ['limit'=> 1]);
    }

    #============== PROV KOTA ===================#

    public function getProvinsi(){
        $path = file_get_contents(public_path('json/provinsi.json'));
        $json = json_decode($path, true);
        return $json;
    }

    public function getKabKota($id){
        $path = file_get_contents(public_path("json/kabupaten/$id.json"));
        $json = json_decode($path, true);
        return $json;
    }

    public function getKecamatan($id){
        $path = file_get_contents(public_path("json/kecamatan/$id.json"));
        $json = json_decode($path, true);
        return $json;
    }

    public function getKelurahan($id){
        $path = file_get_contents(public_path("json/kelurahan/$id.json"));
        $json = json_decode($path, true);
        return $json;
    }

    #============= RAJA ONGKIR ====================#

    public function api_get_province_raja_ongkir(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://pro.rajaongkir.com/api/province",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key:7021b582cede2d3c37dbe64f18d6c29b"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ApiFormatter::error(400, $err);
        }

        $response = json_decode($response, true);
        $response = collect($response['rajaongkir']['results']);

        return ApiFormatter::success(200, 'Province loaded successfully', $response);
    }

    public function api_get_city_raja_ongkir($province_id){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://pro.rajaongkir.com/api/city?province=" . $province_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key:7021b582cede2d3c37dbe64f18d6c29b"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return ApiFormatter::error(400, $err);
        }

        $response = json_decode($response, true);
        $response = collect($response['rajaongkir']['results']);

        return ApiFormatter::success(200, 'City loaded successfully', $response);
    }

    public function api_get_kecamatan_raja_ongkir($city_id){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://pro.rajaongkir.com/api/subdistrict?city=" . $city_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key:7021b582cede2d3c37dbe64f18d6c29b"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return ApiFormatter::error(400, $err);
        }

        $response = json_decode($response, true);
        $response = collect($response['rajaongkir']['results']);

        return ApiFormatter::success(200, 'City loaded successfully', $response);
    }


    #==============================================#

    public function get_data_all($model){

        $model = 'App\\Models\\' . $model;
        $data = $model::all();

        return ApiFormatter::getResponse($data);
    }

    public function get_data_by_id($model, $id){

        $model = 'App\\Models\\' . $model;
        $data = $model::find($id);

        return ApiFormatter::getResponse($data);
    }

    public function get_data_where_field_id_get($model, $where_field, $id){

        $model = 'App\\Models\\' . $model;
        $data = $model::where($where_field, $id)->get();

        return ApiFormatter::getResponse($data);
    }

    public function get_data_where_field_id_first($model, $where_field, $id){

        $model = 'App\\Models\\' . $model;
        $data = $model::where($where_field, $id)->first();

        return ApiFormatter::getResponse($data);
    }

    public function get_call_center(){
        $data = Config::where('config', 'NOMOR CALL CENTER')->first()->value;

        return ApiFormatter::success(200,'Data call center berhasil didapatkan', $data);
    }

    public function get_sales_email(){
        $data = User::SalesActive()->select('email')->get();

        return ApiFormatter::success(200, 'Sales Email berhasil didapatkan', $data);
    }

    #================ DATA =====================#

    #================ DATATABLES ===============#


}
