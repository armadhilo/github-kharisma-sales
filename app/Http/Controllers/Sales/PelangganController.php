<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Helpers\OdooApi;
use App\Http\Requests\PelangganRequest;

use App\Models\User;
use Illuminate\Support\Facades\Route;

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
            $pelanggan = array_values($pelanggan->whereNotNull('keylog')->toArray());
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

        if(!$request->has('lat') || !$request->has('lng')){
            return ApiFormatter::error(400, 'Proses gagal, harap aktifkan lokasi anda untuk melakukan penambahan pelanggan');
        }

        $request->merge(['type' => 'pelanggan' ]);
        $request->merge(['password' => Hash::make($request->password) ]);
        $request->merge(['keylog' => Str::random(40) ]);

        //get provinsi_id
        $path = file_get_contents(public_path('json/provinsi.json'));
        $json = collect(json_decode($path, true));
        $provinsi_id = $json->where('nama', $request->provinsi)->first()['id'];
        $request->merge(['provinsi_id' => $provinsi_id ]);

        //get kota_id
        $path = file_get_contents(public_path("json/kabupaten/$provinsi_id.json"));
        $json = collect(json_decode($path, true));
        $kota_id = $json->where('nama', $request->kota)->first()['id'];
        $request->merge(['kota_id' => $kota_id ]);

        //get kecamatan_id
        $path = file_get_contents(public_path("json/kecamatan/$kota_id.json"));
        $json = collect(json_decode($path, true));
        $kecamatan_id = $json->where('nama', $request->kecamatan)->first()['id'];
        $request->merge(['kecamatan_id' => $kecamatan_id ]);

        //get kelurahan_id
        $path = file_get_contents(public_path("json/kelurahan/$kecamatan_id.json"));
        $json = collect(json_decode($path, true));
        $kelurahan_id = $json->where('nama', $request->kelurahan)->first()['id'];
        $request->merge(['kelurahan_id' => $kelurahan_id ]);

        //format telepon tanpa 0 dan 62
        $telepon_custom = $request->telepon;
        if(substr($request->telepon, 0, 1) == '0'){
            $telepon_custom = substr($request->telepon, 1);
        }elseif(substr($request->telepon, 0, 2) == '62'){
            $telepon_custom = substr($request->telepon, 0,2);
        }

        //create customer odoo
        $request_odoo = new \Illuminate\Http\Request();
        $request_odoo->merge(['name' => $request->nama]);
        $request_odoo->merge(['street' => $request->street]);
        $request_odoo->merge(['phone' => $telepon_custom]);
        $request_odoo->merge(['zip' => $request->zip]);
        $pelanggan_id = OdooApi::createCustomer($request_odoo);

        //append data pelanggan_id from create customer
        $request->merge(['pelanggan_id' => $pelanggan_id[0]]);

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

        //append keylog jika user_keylog masih kosong
        if(empty($user->keylog)){
            $request->request->add(['keylog' => Str::random(40)]);
        }

        if($request->has('password')){
            $request->merge(['password' => Hash::make($request->password) ]);
        }

        if($request->has('provinsi')){
            //get provinsi_id
            $path = file_get_contents(public_path('json/provinsi.json'));
            $json = collect(json_decode($path, true));
            $provinsi_id = $json->where('nama', $request->provinsi)->first()['id'];
            $request->merge(['provinsi_id' => $provinsi_id ]);

            //get kota_id
            $path = file_get_contents(public_path("json/kabupaten/$provinsi_id.json"));
            $json = collect(json_decode($path, true));
            $kota_id = $json->where('nama', $request->kota)->first()['id'];
            $request->merge(['kota_id' => $kota_id ]);

            //get kecamatan_id
            $path = file_get_contents(public_path("json/kecamatan/$kota_id.json"));
            $json = collect(json_decode($path, true));
            $kecamatan_id = $json->where('nama', $request->kecamatan)->first()['id'];
            $request->merge(['kecamatan_id' => $kecamatan_id ]);

            //get kelurahan_id
            $path = file_get_contents(public_path("json/kelurahan/$kecamatan_id.json"));
            $json = collect(json_decode($path, true));
            $kelurahan_id = $json->where('nama', $request->kelurahan)->first()['id'];
            $request->merge(['kelurahan_id' => $kelurahan_id ]);
        }

        $request->merge(['type' => 'pelanggan' ]);

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
