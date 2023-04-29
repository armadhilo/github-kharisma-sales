<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\ClaimKodeVoucherRequest;
use App\Models\KodeVoucher;
use App\Models\KodeVoucherUser;

class KodeVoucherController extends Controller
{
    public function index(){

        $data_voucher = KodeVoucher::active()->get();
        $data_voucher_claim = KodeVoucher::mustClaim()->whereHas('kode_voucher_user', function($query){
            $query->active();
        })->get();

        $allItems = new \Illuminate\Database\Eloquent\Collection;
        $allItems = $allItems->merge($data_voucher);
        $allItems = $allItems->merge($data_voucher_claim);

        //MENCARI VOUCHER YANG MASIH DAPAT DIGUNAKAN
        $array = [];
        foreach($allItems as $item){
            if($item->maksimal_penggunaan > $item->total_penggunaan){
                $array[] = $item;
            }
        }

        $message = ' Data kode voucher berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $array);
    }


    public function detail($kode_voucher){

        $kode_voucher = KodeVoucher::where('kode_voucher', $kode_voucher)->first();
        if(empty($kode_voucher)){
            $message = 'Kode Voucher tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Detail Kode Voucher berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $kode_voucher);
    }

    public function claim(ClaimKodeVoucherRequest $request){

        $kode_voucher = KodeVoucher::active()->where('kode_voucher', $request->kode_voucher)->first();

        if(!empty($kode_voucher)){
            if(!empty($kode_voucher)){
                return ApiFormatter::error(400, 'Kode Voucher telah anda claim');
            }
        }else{
            $kode_voucher = KodeVoucher::mustClaim()->where('kode_voucher', $request->kode_voucher)->first();
            if(empty($kode_voucher)){
                return ApiFormatter::error(404, 'Kode Voucher tidak ditemukan');
            }

            $kode_voucher_user = KodeVoucherUser::where(['user_id' => auth('api')->user()->id, 'kode_voucher_id' => $kode_voucher->id])->first();
            if(!empty($kode_voucher_user)){
                return ApiFormatter::error(400, 'Kode Voucher telah anda claim');
            }
        }

        if($kode_voucher->maksimal_penggunaan <= $kode_voucher->total_penggunaan){
            return ApiFormatter::error(400, 'Kode Voucher sudah limit penggunaan');
        }

        $kode_voucher_user = KodeVoucherUser::create([
            'user_id' => auth('api')->user()->id,
            'kode_voucher_id' => $kode_voucher->id,
        ]);

        $kode_voucher = KodeVoucherUser::with('kode_voucher')->find($kode_voucher_user->id);

        $message = 'Kode Voucher berhasil di claim';
        return ApiFormatter::success(200, $message, $kode_voucher);
    }
}
