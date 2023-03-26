<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\SalesLogRequest;

use App\Models\SalesLog;

class SalesLogController extends Controller
{
    public function store(SalesLogRequest $request){

        SalesLog::create([
            'sales_id' => auth('api')->user()->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        $message = 'Sales log created successfully';
        return ApiFormatter::success(200, $message);
    }
}
