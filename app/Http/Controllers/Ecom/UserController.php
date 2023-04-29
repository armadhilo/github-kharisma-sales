<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Models\User;

use App\Http\Requests\PelangganRequest;

class UserController extends Controller
{
    public function detail(){
        $message = 'User loaded successfully';
        return ApiFormatter::success(200, $message, auth('api')->user());
    }

    public function update(PelangganRequest $request){
        $user = User::where(['id' => auth('api')->user()->id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $user->fill($request->all())->save();
        $message = 'User Updated Successfully';

        $user = User::find(auth('api')->user()->id);
        return ApiFormatter::success(200, $message, $user);
    }
}
