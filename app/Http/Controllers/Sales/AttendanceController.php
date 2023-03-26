<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\PelangganRequest;
use App\Models\SalesAttendance;
use App\Models\User;

class AttendanceController extends Controller
{
    public function store(AttendanceRequest $request){

        $sales_attendance = SalesAttendance::where(['sales_id' => auth('api')->user()->id])->whereDate('date_start', $request->date)->whereNotNull('date_end')->first();
        if(!empty($sales_attendance)){
            $message = 'sales have finish attendance today';
            return ApiFormatter::error(404, $message);
        }

        $attendance_today = SalesAttendance::where(['sales_id' => auth('api')->user()->id])->whereNull('date_end')->first();
        $status = 'check Out';
        if(empty($attendance_today)){
            $status = 'check In';
            SalesAttendance::create([
                'sales_id' => auth('api')->user()->id,
                'date_start' => $request->date,
                'time_start' => $request->time
            ]);
        }else{
            $attendance_today->date_end = $request->date;
            $attendance_today->time_end = $request->time;
            $attendance_today->save();
        }

        $message = "You has been $status on $request->time successfully";
        return ApiFormatter::success(200, $message);
    }

}
