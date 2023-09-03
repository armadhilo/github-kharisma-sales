<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use \Carbon\Carbon;

use App\Helpers\ApiFormatter;
use App\Http\Requests\SalesLogRequest;
use App\Models\SalesAttendance;
use App\Models\SalesLog;

class SalesLogController extends Controller
{
    public function store(SalesLogRequest $request){

        $attendance_today = SalesAttendance::where(['sales_id' => auth('api')->user()->pelanggan_id])->whereNull('date_end')->first();
        if(!empty($attendance_today)){

            //first insert to db jika di hari itu belum ada log
            $sales_log = SalesLog::where('sales_id', auth('api')->user()->pelanggan_id)->whereDate('created_at', Carbon::now())->first();
            if(empty($sales_log)){
                SalesLog::create([
                    'sales_id' => auth('api')->user()->pelanggan_id,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                ]);

                $message = 'Sales log created successfully';
                return ApiFormatter::success(200, $message);
            }

            $sales_log = SalesLog::where('sales_id', auth('api')->user()->pelanggan_id)->orderBy('id','desc')->first();
            if(!empty($sales_log)){
                $distance = $this->calculateDistance($sales_log->lat, $sales_log->lng, $request->lat, $request->lng);

                //jika jarak lebih dari 1km dari lat long sebelumnya insert to db
                // if((int) $distance >= 1){
                    SalesLog::create([
                        'sales_id' => auth('api')->user()->pelanggan_id,
                        'lat' => $request->lat,
                        'lng' => $request->lng,
                    ]);

                    $message = 'Sales log created successfully';
                    return ApiFormatter::success(200, $message);
                // }
            }
        }
    }
}
