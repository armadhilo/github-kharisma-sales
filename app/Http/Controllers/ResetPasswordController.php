<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

use \Carbon\Carbon;
use DB;

use App\Libraries\QueryLibraries;
use App\Helpers\App;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;

class ResetPasswordController extends Controller
{

    public function index(Request $request){
        $data['token'] = $request->token;
        return view('auth.reset-password', $data);
    }

    public function reset_password(ResetPasswordRequest $request){

        $user = User::where('email', $request->email)->first();

        //jika error tetap kirim respon sukses
        if(empty($user)){
            return ApiFormatter::success(200,'Berhasil request dan email verifikasi akan terkirim jika email anda valid, silahkan cek spam anda');
        }

        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $data = [
            'nama' => $user->nama,
            'link' => URL::to('/') . '/reset-password?token=' . $token,
        ];

        $email = $user->email;
        Mail::to($email)->send(new ResetPassword($data));

        return ApiFormatter::success(200,'Yeay! Berhasil request dan email verifikasi akan terkirim jika email anda valid, silahkan cek spam anda');
    }

    public function change_password(request $request){

        $password_resets = DB::table('password_resets')->where(['token' => $request->token])->first();
        if(empty($password_resets)){
            return view('errors/404');
        }

        $data['data'] = $password_resets;
        return view('login.change-password', $data);
    }

    public function set_password(request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'token' => 'required',
            'password' => 'required| min:6',
            'ulangi_password' => 'required|same:password',
		]);

		if($validator->fails()) {
            return [
                'status' => 300,
				'message' => $validator->errors()->first()
			];
		}

        User::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('email', $request->email)->delete();

        ApiFormatter::success(200, 'Password berhasil direset silahkan login menggunakan password baru');
    }
}
