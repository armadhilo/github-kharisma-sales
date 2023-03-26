<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

use App\Helpers\ApiFormatter;
use App\Http\Requests\KunjunganRequest;

use Carbon\Carbon;

use App\Models\Kunjungan;

class KunjunganController extends Controller
{
    public function store(KunjunganRequest $request){

        $kunjungan = Kunjungan::where(['sales_id' => auth('api')->user()->id, 'pelanggan_id' => $request->pelanggan_id])->whereDate('date', Carbon::now())->first();
        $status = 'Start Kunjungan';
        if(empty($kunjungan)){
            Kunjungan::create([
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

        $message = "You has been $status on ".Carbon::now()->format('H:i')." successfully";
        return ApiFormatter::success(200, $message);
    }

}
