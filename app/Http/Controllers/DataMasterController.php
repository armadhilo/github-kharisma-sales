<?php

namespace App\Http\Controllers;

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

class DataMasterCOntroller extends Controller
{

    #============== TRY CODE ====================#

    public function try_code(){
        return Kunjungan::with('sales','pelanggan')->get();
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
