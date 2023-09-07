<?php

namespace App\Http\Controllers\BackOffice;

use App\Helpers\OdooApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesRequest;
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

    public function store_update(SalesRequest $request){

        if(substr($request->telepon, 0, 2) != '08'){
            return [
                'status' => 300,
                'message' => 'Nomer harus dimulai dari 08xxx',
            ];
        }

        if($request->type != 'update'){
            $request->request->add(['password' =>Hash::make($request->password)]);
            $request->request->add(['type' => 'sales']);
        }

        if(!empty($request->password)){
            $request->merge(['password' =>Hash::make($request->password)]);
        }

        $user = User::find($request->id);
        $parameter['pelanggan_id'] = $user->pelanggan_id;
        $parameter['name'] = $request->nama;
        $parameter['email'] = $request->email;
        $parameter['mobile'] = $request->telepon;

        $odoo_api = OdooApi::updateEmailSales($parameter);
        if(!$odoo_api){
            return [
                'status' => 300,
                'message' => 'Terjadi kesalahan saat syncronize dengan Odoo',
            ];
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
