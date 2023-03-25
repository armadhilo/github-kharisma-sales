<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;

class AuthController extends Controller
{

    public function login(request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|max:255',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return ApiFormatter::validate($validator->errors());
        }

        $data = [
            'email'     => $request->email,
            'password'  => $request->password,
        ];

        Auth::attempt($data);

        if (Auth::check()) {
            $user = Auth::user();
            $token = $user->createToken($user->name)->accessToken;

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
