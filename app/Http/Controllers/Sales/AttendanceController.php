<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

use App\Helpers\ApiFormatter;
use App\Models\SalesAttendance;

class AttendanceController extends Controller
{
    public function store(){

        $sales_attendance = SalesAttendance::where(['sales_id' => auth('api')->user()->id])->whereDate('date_start', Carbon::now())->whereNotNull('date_end')->first();
        if(!empty($sales_attendance)){
            $message = 'sales have finish attendance today';
            return ApiFormatter::error(400, $message);
        }

        $attendance_today = SalesAttendance::where(['sales_id' => auth('api')->user()->id])->whereNull('date_end')->first();
        $status = 'check Out';
        if(empty($attendance_today)){
            $status = 'check In';
            SalesAttendance::create([
                'sales_id' => auth('api')->user()->id,
                'date_start' => Carbon::now(),
                'time_start' => Carbon::now()->format('H:i')
            ]);
        }else{
            $attendance_today->date_end = Carbon::now();
            $attendance_today->time_end = Carbon::now()->format('H:i');
            $attendance_today->save();
        }

        $message = "You has been $status on ".Carbon::now()->format('H:i')." successfully";
        return ApiFormatter::success(200, $message);
    }

}
