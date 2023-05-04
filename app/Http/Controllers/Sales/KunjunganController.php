<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use App\Http\Requests\KunjunganRequest;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Kunjungan;

class KunjunganController extends Controller
{

    public function store(KunjunganRequest $request){

        //check apakah ada kunjungan yang masih aktif
        $kunjungan = Kunjungan::where(['sales_id' => auth('api')->user()->id])->whereNull('total_time')->first();
        if(!empty($kunjungan)){

            if($kunjungan->pelanggan_id != $request->pelanggan_id){

                $message = 'Masih terdapat toko yang sedang anda visit silahkan selesaikan dahulu sebelum memulai kunjungan berikutnya';
                return ApiFormatter::error(400, $message);
            }
        }

        $kunjungan = Kunjungan::with('pelanggan')->where(['sales_id' => auth('api')->user()->id, 'pelanggan_id' => $request->pelanggan_id])->whereDate('date', Carbon::now())->first();
        $status = 'Start Kunjungan';
        if(empty($kunjungan)){

            $kunjungan = Kunjungan::create([
                'sales_id' => auth('api')->user()->id,
                'pelanggan_id' => $request->pelanggan_id,
                'date' => Carbon::now(),
                'time_start' => Carbon::now()->format('H:i'),
            ]);

        }else{

            $status = 'End Kunjungan';
            $concat_datetime = $kunjungan->date ." ". $kunjungan->time_start;
            $kunjungan_start = Carbon::parse("$concat_datetime");

            //mengambil selisih menit jika true maka terlambat
            $diff = $kunjungan_start->diffInMinutes(Carbon::now()->format('H:i'), false);

            $kunjungan->time_end = Carbon::now()->format('H:i');
            $kunjungan->total_time = $diff;
            $kunjungan->save();
        }

        $kunjungan['key_login'] = $kunjungan->pelanggan->keylog;

        $message = "You has been $status on ".Carbon::now()->format('H:i')." successfully";
        return ApiFormatter::success(200, $message, $kunjungan);
    }

    public function check_today(){

        $kunjungan = Kunjungan::with('pelanggan')->where(['sales_id' => auth('api')->user()->id])->whereNull('time_end')->whereDate('date', Carbon::now())->first();
        if(empty($kunjungan)){
            $message = 'You dont have active kunjungan today';
            return ApiFormatter::error(400, $message);
        }

        $message = "Data kunjungan retrieved successfully";
        $sales_attendance['user_login'] = auth('api')->user();
        return ApiFormatter::success(200, $message, $kunjungan);
    }

}
