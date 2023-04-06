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
        $models->execute_kw($db, $uid, $password,
            'res.partner', 'check_access_rights',
            array('read'), array('raise_exception' => false));

        return $models->execute_kw($db, $uid, $password,
        'res.partner', 'search', array(
            array(array('is_company', '=', true),
                  array('customer', '=', true))));
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

    public function get_data_where_field_id($model, $where_field, $id){

        $model = 'App\\Models\\' . $model;
        $data = $model::where($where_field, $id)->first();

        return ApiFormatter::getResponse($data);
    }
    #================ DATA =====================#

    #================ DATATABLES ===============#


}
