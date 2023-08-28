<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DataTables;

class ConfigController extends Controller
{
    public function index(){
        return view('admin.config.index');
    }

    public function detail($id){
        return Config::find($id);
    }

    public function datatables(){
        $data = Config::orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function store_update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'value' => 'required',
        ]);

        if($validator->fails()){
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        Config::find($request->id)->update(['value'=>$request->value]);

        return [
            'status' => 200,
            'message' => 'Data Berhasil Disimpan',
        ];

    }
}
