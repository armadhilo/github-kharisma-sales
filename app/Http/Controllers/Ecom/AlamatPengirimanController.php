<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Helpers\OdooApi;
use App\Http\Requests\PelangganRequest;
use App\Http\Requests\Ecom\AlamatPengirimanRequest;

use App\Models\AlamatPengiriman;

class AlamatPengirimanController extends Controller
{

    public function index(){

        $alamat_pengiriman = AlamatPengiriman::active()->get();

        if(!count($alamat_pengiriman)){
            $message = 'Alamat Pengiriman not found';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Data Alamat Pengiriman berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $alamat_pengiriman);
    }

    public function detail($id){

        $alamat_pengiriman = AlamatPengiriman::where(['id' => $id])->first();
        if(empty($alamat_pengiriman)){
            $message = 'Alamat Pengiriman tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Alamat Pengiriman berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $alamat_pengiriman);
    }

    public function store_update(AlamatPengirimanRequest $request){

        //jika bukan update cek karena alamat pengiriman cuma boleh ada 1
        if(!$request->has('id')){
            $alamat_pengiriman = AlamatPengiriman::where('user_id', auth('api')->user()->id)->count();
            if($alamat_pengiriman > 0){
                return ApiFormatter::error(400, 'Oops! anda tidak dapat menambahkan alamat pengiriman baru');
            }
        }

        $request->request->add(['user_id' => auth('api')->user()->id]);

        $count_alamat_pengiriman = AlamatPengiriman::where('user_id', $request->user_id)->count();
        if($count_alamat_pengiriman == 0){
            $request->request->add(['alamat_utama' => '1']);
        }

        //format telepon tanpa 0 dan 62
        $telepon_custom = $request->no_telepon;
        if(substr($request->no_telepon, 0, 1) == '0'){
            $telepon_custom = substr($request->no_telepon, 1);
        }elseif(substr($request->no_telepon, 0, 2) == '62'){
            $telepon_custom = substr($request->no_telepon, 0,2);
        }

        //update data di odoo
        $request_odoo = new \Illuminate\Http\Request();
        $request_odoo->merge(['pelanggan_id' => auth('api')->user()->pelanggan_id]);
        $request_odoo->merge(['street' => $request->alamat]);
        $request_odoo->merge(['phone' => $telepon_custom]);
        $request_odoo->merge(['zip' => $request->kode_pos]);

        OdooApi::updateCustomer($request_odoo);

        $alamat_pengiriman = AlamatPengiriman::updateOrCreate(['id' => $request->id],$request->all() );

        $action = $alamat_pengiriman->wasRecentlyCreated ? 'ditambahkan' : 'diupdate';

        $message = 'Alamat Pengiriman berhasil ' . $action;
        return ApiFormatter::success(200, $message);
    }

    public function setting_alamat_utama($id){

        $alamat_pengiriman = AlamatPengiriman::find($id);

        //delete alamat utama
        AlamatPengiriman::where(['alamat_utama' => 1, 'user_id' => auth('api')->user()->id])->update(['alamat_utama' => 0]);

        $alamat_pengiriman->alamat_utama = 1;
        $alamat_pengiriman->save();

        $message = 'Alamat Pengiriman utama berhasil disetting';
        return ApiFormatter::success(200, $message);
    }

    public function delete($id){

        $alamat_pengiriman = AlamatPengiriman::find($id);

        //apabila alamat yang dihapus adalah alamat utama
        //maka setting alamat lain menjadi alamat utama
        if($alamat_pengiriman->alamat_utama == 1){
            $new_alamat_utama = AlamatPengiriman::where(['user_id' => $alamat_pengiriman->user_id])->where('id','!=',$alamat_pengiriman->id)->first();
            if(!empty($new_alamat_utama)){
                $new_alamat_utama->alamat_utama = 1;
                $new_alamat_utama->save();
            }
        }

        $alamat_pengiriman->delete();

        $message = 'Alamat Pengiriman berhasil dihapus';
        return ApiFormatter::success(200, $message);
    }
}
