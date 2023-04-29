<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\ForgotPasswordRequest;
use App\Http\Requests\Ecom\LoginRequest;

use App\Models\User;

class AuthController extends Controller
{

    public function login(LoginRequest $request){

        $sales = [];

        if($request->has('keylog')){
            $sales = User::where(['email' => $request->sales_email, 'type' => '1'])->first();
            $pelanggan = User::where(['keylog' => $request->keylog, 'type' => '2'])->first();

            if(empty($sales) || empty($pelanggan)){
                $message = 'Data anda tidak ditemukan pada sistem kami.';
                return ApiFormatter::error(400, $message);
            }

            session(['sales_id' => $sales->id]);
            $sales = [
                'id' => $sales->id,
                'nama' => $sales->nama,
                'email' => $sales->email,
            ];

            Auth::login($pelanggan);

        }else{

            $data = [
                'email'     => $request->email,
                'password'  => $request->password,
            ];

            Auth::attempt($data);

        }

        if (Auth::check()) {

            if(empty(auth()->user()->keylog)){
                $message = 'Account anda tidak aktif silahkan hubungi sales kami.';
                return ApiFormatter::error(400, $message);
            }

            $user = Auth::user();
            $token = $user->createToken($user->name)->accessToken;

            $message = 'Successfully login.';
            $data = [
                'user' => $user,
                'sales' => $sales,
                'token' => $token,
            ];

            return ApiFormatter::success(200, $message, $data);
        }else{

            $message = 'Data anda tidak ditemukan pada sistem kami.';
            return ApiFormatter::error(400, $message);
        }
    }

    public function forgot_password(ForgotPasswordRequest $request){

        $user = User::where(['email' => $request->email, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'Forgot password gagal, Pelanggan tidak ditemukan';
            return ApiFormatter::error(404, $message);
        }

        $message = 'Email Forgot Password telah dikirim ke email anda, harap segera cek email anda';
        return ApiFormatter::success(200, $message);
    }

    public function logout(){
        auth('api')->user()->AauthAcessToken()->delete();

        $message = 'Successfully logout.';
        return ApiFormatter::success(200, $message);
    }
}
