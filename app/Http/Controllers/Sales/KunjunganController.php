<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use App\Http\Requests\KunjunganRequest;
use App\Models\Config;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Kunjungan;
use App\Models\SalesAttendance;

class KunjunganController extends Controller
{

    public function store(KunjunganRequest $request){

        //check apakah absensi masih berjalan
        $sales_attendance = SalesAttendance::where('sales_id', auth('api')->user()->id)->whereDate('date_start', Carbon::now())->first();
        if(empty($sales_attendance)){
            $message = 'Tidak ada absensi yang aktif harap lakukan absensi terlebih dahulu';
            return ApiFormatter::error(400, $message);
        }

        $sales_attendance = SalesAttendance::where('sales_id', auth('api')->user()->id)->whereDate('date_start', Carbon::now())->whereNotNull('date_end')->first();
        if(!empty($sales_attendance)){
            $message = 'Anda sudah checkout absensi hari ini, tidak dapat melakukan kunjungan';
            return ApiFormatter::error(400, $message);
        }

        //check apakah ada pelanggan tersebut
        $user = User::find($request->pelanggan_id);
        if(empty($user)){
            $message = 'Pelanggan tidak ditemukan atau sudah tidak aktif';
            return ApiFormatter::error(400, $message);
        }

        $radius = $this->cekRadius($user->lng, $user->lat, $request->lng, $request->lat);
        $max_radius = Config::where('config', 'MAX RADIUS KUNJUNGAN')->first()->value;
        if($radius > $max_radius){
            $message = "Oops! Radius anda adalah $radius sedangkan radius maksimal adalah $max_radius";
            return ApiFormatter::error(400, $message);
        }

        //check apakah ada kunjungan yang masih aktif
        $kunjungan = Kunjungan::where(['sales_id' => auth('api')->user()->id])->whereNull('total_time')->first();
        if(!empty($kunjungan)){

            if($kunjungan->pelanggan_id != $request->pelanggan_id){
                $message = 'Masih terdapat toko yang sedang anda visit silahkan selesaikan dahulu sebelum memulai kunjungan berikutnya';
                return ApiFormatter::error(400, $message);
            }
        }

        $kunjungan = Kunjungan::with('pelanggan')->where(['sales_id' => auth('api')->user()->id, 'pelanggan_id' => $request->pelanggan_id])->whereNull('time_end')->first();
        $status = 'Start Kunjungan';
        if(!empty($kunjungan)){

            $status = 'End Kunjungan';
            $concat_datetime = $kunjungan->date ." ". $kunjungan->time_start;
            $kunjungan_start = Carbon::parse("$concat_datetime");

            //mengambil selisih menit jika true maka terlambat
            $diff = $kunjungan_start->diffInMinutes(Carbon::now()->format('H:i'), false);

            $kunjungan->time_end = Carbon::now()->format('H:i');
            $kunjungan->total_time = $diff;
            $kunjungan->save();

        }else{

            $kunjungan = Kunjungan::create([
                'sales_id' => auth('api')->user()->id,
                'pelanggan_id' => $request->pelanggan_id,
                'date' => Carbon::now(),
                'time_start' => Carbon::now()->format('H:i'),
            ]);
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

    private function cekRadius($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius of the Earth in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance);
    }

}
