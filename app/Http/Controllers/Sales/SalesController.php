<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\SalesLogRequest;

use App\Models\User;

class SalesController extends Controller
{
    public function detail($id){

        $user = User::where(['id' => $id, 'type' => '1'])->first();
        if(empty($user)){
            $message = 'Sales not found';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Sales loaded successfully';
        return ApiFormatter::success(200, $message, $user);
    }
}
