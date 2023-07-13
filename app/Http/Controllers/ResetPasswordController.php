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
        $user = DB::table('password_resets')->where('token', '=', $request->token)->first();
        if ($user === null) {
            return view('errors.404');
        }
        $data['token'] = $request->token;
        return view('auth.reset-password', $data);
    }

    public function reset_password(ResetPasswordRequest $request){

        $user = User::where('email', $request->email)->first();

        //jika error tetap kirim respon sukses
        if(empty($user)){
            return ApiFormatter::error(400,'Email anda tidak terdaftar di sistem kami');
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
            return view('errors.404');
        }

        $data['data'] = $password_resets;
        return view('login.change-password', $data);
    }

    public function set_password(request $request){
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required| min:6',
            'confirm-password' => 'required|same:password',
		]);

		if($validator->fails()) {
            return [
                'status' => 300,
				'message' => $validator->errors()->first()
			];
		}

        DB::beginTransaction();

        try{
            $email =  DB::table('password_resets')->where('token', $request->token)->first();
            if(empty($email)){
                return [
                    'status' => 300,
                    'message' => 'Token sudah expired, silahkan cek email anda untuk menerima link baru'
                ];
            }
    
            $email = $email->email;

            $user = User::where('email', $email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            DB::table('password_resets')->where('email', $email)->delete();

            //destroy user and token
            $user->AauthAcessToken()->delete();
            Session::flush();

            DB::commit();
    
            return [
                'status' => 201,
                'message' => 'Password Berhasil Diubah',
                'link' => 'reset-password-success'
            ];
        }

        catch(\Exception $e){
    
            DB::rollback();
    
            return [
                'status'     => 300, // GAGAL
                'message'       => (env('APP_DEBUG', 'true') == 'true')? $e->getMessage() : 'Operation error'
            ];
    
        }
    }
}
