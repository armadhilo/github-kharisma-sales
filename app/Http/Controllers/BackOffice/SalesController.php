<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use DB;
use DataTables;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index(){
        return view('admin.sales.index');
    }

    public function detail($id){
        return User::where('type', 1)->find($id);
    }

    public function datatables(){
        $data = User::where('type', 1)->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function store_update(request $request){
        $array_validation = [
            'nama' => 'required',
            'email' => 'required|email:rfc,dns',
            'telepon' => 'required|min:10|max:12',
            'alamat' => 'required',
            'password' => 'required|min:6'
        ];

        $validator = Validator::make($request->all(), $array_validation);

		if($validator->fails()) {
			return [
				'status' => 300,
				'message' => $validator->errors()->first()
			];
		}


        if(substr($request->telepon, 0, 2) != '08'){
            return [
                'status' => 300,
                'message' => 'Nomer harus dimulai dari 08xxx',
            ];
        }

        if($request->type != 'update'){
            $request->request->add(['password' =>Hash::make($request->password)]);
            $request->request->add(['type' => 1]);
        }

        User::updateOrCreate(['id' => $request->id],$request->all() );

        return [
            'status' => 200, // SUCCESS
            'message' => 'Data berhasil disimpan'
        ];
    }

    public function delete($id){
        $delete = User::find($id);

        if($delete->type !== 'sales'){
            return [
                'status' => 300,
                'message' => 'Data Bukan Termasuk Sales'
            ];
        }

        if($delete <> ""){
            $delete->delete();
            return [
                'status' => 200,
                'message' => 'Data berhasil dihapus'
            ];
        }

        return [
            'status' => 300,
            'message' => 'Data tidak ditemukan'
        ];
    }
}
