<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\PelangganRequest;

use App\Models\User;

class PelangganController extends Controller
{
    public function index(request $request){

        #=========PARAMETER=========#
        #pagination -> 1 : active | 0 : inactive
        #filter
            # status_pelanggan -> active | all [default all]

        $pelanggan = User::where(['type' => 2])
            ->where(function($query) use ($request){
                $query->orWhere('nama', 'LIKE', "%$request->search%")
                ->orWhere('nama_toko', 'LIKE', "%$request->search%")
                ->orWhere('email', 'LIKE', "%$request->search%")
                ->orWhere('alamat_toko', 'LIKE', "%$request->search%")
                ->orWhere('kota', 'LIKE', "%$request->search%")
                ->orWhere('kecamatan', 'LIKE', "%$request->search%")
                ->orWhere('kelurahan', 'LIKE', "%$request->search%");
            })
            ->get();

        if($request->has('status_pelanggan') && $request->status_pelanggan == 'active'){
            $pelanggan = $pelanggan->whereNotNull('keylog')->all();
        }

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
        $request->merge(['keylog' => Str::random(40) ]);

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
