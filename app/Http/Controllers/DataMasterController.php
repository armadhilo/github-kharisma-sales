<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kavist\RajaOngkir\Facades\RajaOngkir;

use DataTables;
use Carbon\Carbon;

use App\Models\MasterPackage;

use App\Helpers\ApiFormatter;

class DataMasterCOntroller extends Controller
{
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
