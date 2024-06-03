<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{

    public function login(LoginRequest $request){

        $data = [
            'email'     => $request->email,
            'password'  => $request->password,
        ];

        Auth::attempt($data);

        if (Auth::check()) {

            // if type pelanggan
            // if(Auth::user()->type == 'pelanggan'){
            //     $message = 'Hanya role Sales yang diperbolehkan login ke aplikasi';
            //     return ApiFormatter::error(400, $message);
            // }

            $user = Auth::user();
            $token = $user->createToken(Auth::user()->email)->accessToken;

            $message = 'Successfully login.';
            $data = [
                'user' => $user,
                'token' => $token,
            ];

            return ApiFormatter::success(200, $message, $data);
        }else{

            $message = 'These credentials do not match our records.';
            return ApiFormatter::error(400, $message);
        }
    }

    public function logout(){
        auth('api')->user()->AauthAcessToken()->delete();

        $message = 'Successfully logout.';
        return ApiFormatter::success(200, $message);
    }
}
