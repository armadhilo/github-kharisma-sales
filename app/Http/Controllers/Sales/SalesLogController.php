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

        $attendance_today = SalesAttendance::where(['sales_id' => auth('api')->user()->id])->whereNull('date_end')->first();
        if(!empty($attendance_today)){

            //first insert to db jika di hari itu belum ada log
            $sales_log = SalesLog::where('sales_id', auth('api')->user()->id)->whereDate('created_at', Carbon::now())->first();
            if(empty($sales_log)){
                SalesLog::create([
                    'sales_id' => auth('api')->user()->id,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                ]);

                $message = 'Sales log created successfully';
                return ApiFormatter::success(200, $message);
            }

            $sales_log = SalesLog::where('sales_id', auth('api')->user()->id)->orderBy('id','desc')->first();
            if(!empty($sales_log)){
                $distance = $this->calculateDistance($sales_log->lat, $sales_log->lng, $request->lat, $request->lng);

                //jika jarak lebih dari 1km dari lat long sebelumnya insert to db
                // if((int) $distance >= 1){
                    SalesLog::create([
                        'sales_id' => auth('api')->user()->id,
                        'lat' => $request->lat,
                        'lng' => $request->lng,
                    ]);

                    $message = 'Sales log created successfully';
                    return ApiFormatter::success(200, $message);
                // }
            }
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = "km") {
        $theta = $lon1 - $lon2;
        $distance = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515; // distance in miles

        if ($unit == "km") {
            $distance = $distance * 1.609344; // convert miles to kilometers
        }

        return $distance;
    }
}
