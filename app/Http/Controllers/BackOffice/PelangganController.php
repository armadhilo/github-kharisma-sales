<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\PelangganRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DataTables;


class PelangganController extends Controller
{
    
    public function index(){
        return view('admin.pelanggan.index');
    }

    public function add(){
        return view('admin.pelanggan.add');
    }

    public function detail($id){
        $data['data'] =  User::where('type', 2)->find($id);
        return view('admin.pelanggan.add',$data);
    }

    public function datatables(){
        $data = User::where('type', 2)->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function store_update(PelangganRequest $request){

        if(substr($request->telepon, 0, 2) != '08'){
            return [
                'status' => 300,
                'message' => 'Nomer harus dimulai dari 08xxx',
            ];
        }

        if($request->type != 'update'){
            $request->request->add(['type' => 2]);
        }

        $request->request->add(['password' =>Hash::make($request->password)]);


        User::updateOrCreate(['id' => $request->id],$request->all() );

        return [
            'status' => 201, // SUCCESS
            'message' => 'Data berhasil disimpan',
            'link' => '/admin/pelanggan',
        ];
    }

    public function delete($id){
        $delete = User::find($id);

        if($delete->type !== 'pelanggan'){
            return [
                'status' => 300,
                'message' => 'Data Bukan Termasuk Pelanggan'
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
