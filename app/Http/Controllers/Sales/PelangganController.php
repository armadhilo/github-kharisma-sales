<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\PelangganRequest;

use App\Models\User;

class PelangganController extends Controller
{
    public function index(request $request){

        #=========PARAMETER=========#
        #pagination -> 1 : active | 0 : inactive

        $pelanggan = User::where(['type' => 2])
        ->when($request->nama, function ($query) use ($request) {
            return $query->where('nama', 'like', '%'.$request->nama.'%');
        })
        ->when($request->nama_toko, function ($query) use ($request) {
            return $query->where('nama_toko', 'like', '%'.$request->nama_toko.'%');
        })
        ->when($request->alamat_toko, function ($query) use ($request) {
            return $query->where('alamat_toko', 'like', '%'.$request->alamat_toko.'%');
        })
        ->when($request->kota, function ($query) use ($request) {
            return $query->where('kota', 'like', '%'.$request->kota.'%');
        })
        ->when($request->kecamatan, function ($query) use ($request) {
            return $query->where('kecamatan', 'like', '%'.$request->kecamatan.'%');
        })
        ->when($request->kelurahan, function ($query) use ($request) {
            return $query->where('kelurahan', 'like', '%'.$request->kelurahan.'%');
        })
        ->get();

        if(!count($pelanggan)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        //pagination formatting
        $page_limit = 10;
        if(!$request->has('pagination') || $request->pagination == 1){

            //setting pagination
            $array = $this->paginate($pelanggan, $page_limit)->withPath(url()->current());

            $message = 'Data User fetch successfully';
            return ApiFormatter::pagination($message, $array);
        }

        $message = 'User listed successfully';
        return ApiFormatter::success(200, $message, $pelanggan);
    }

    public function store(PelangganRequest $request){

        $request->merge(['password' => Hash::make($request->password) ]);

        User::create($request->all());

        $message = 'User created successfully';
        return ApiFormatter::success(200, $message);
    }

    public function detail($id){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $message = 'User loaded successfully';
        return ApiFormatter::success(200, $message, $user);
    }

    public function update($id, PelangganRequest $request){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $user->fill($request->all())->save();
        $message = 'User Updated Successfully';
        return ApiFormatter::success(200, $message);
    }

    public function delete($id){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $user->delete();
        $message = 'User Deleted Successfully';
        return ApiFormatter::success(200, $message);

    }
}
