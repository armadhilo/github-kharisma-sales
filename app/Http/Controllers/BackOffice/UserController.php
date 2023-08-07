<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class UserController extends Controller
{
    public function index(){
        return view('admin.user.index');
    }

    public function detail($id){
        return User::where('type', 0)->find($id);
    }

    public function datatables(){
        $data = User::where('type', 0)->orderby('id','desc')->get();

        return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function store_update(UserRequest $request){
        
        if(substr($request->telepon, 0, 2) != '08'){
            return [
                'status' => 300,
                'message' => 'Nomer harus dimulai dari 08xxx',
            ];
        }

        
        if($request->access_group == 'all access' || $request->access_menu == ['1', '2', '3', '4', '5', '6','7', '8', '9', '10', '11',]){
            $request->merge([
                'access_menu' => 0
            ]);
        }
        
        if(is_null($request->id)){
            $request->request->add(['password' =>Hash::make($request->password)]);
            $request->request->add(['type' => 'superadmin']);
        }
        
        User::updateOrCreate(['id' => $request->id],$request->all() );

        if($request->id == Auth::user()->id){
            return [
                'status' => 201, // SUCCESS
                'link' => '/admin/dashboard',
                'message' => 'Data berhasil disimpan'
            ];
        } else {
            return [
                'status' => 200, // SUCCESS
                'message' => 'Data berhasil disimpan'
            ];
        }

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
