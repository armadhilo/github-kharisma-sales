<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(){
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request){
        $validate = [
            'email'            => 'required',
            'password'         => 'required',
        ];

        $messages = [
            'email.required'   => 'Email wajib di isi',
            'password.required'     => 'Password wajib diisi',
        ];

        $validator = Validator::make($request->all(), $validate, $messages);

        if($validator->fails()){
            return [
                'status' => 300,
                'message' => $validator->errors()->first()
            ];
        }

        $data = [
            'email'     => $request->input('email'),
            'password'  => $request->input('password'),
        ];

        auth::attempt($data, true); // add parameter true to prevent logout when refresh

        if (Auth::check()) {

            if(auth()->user()->type != 'superadmin'){
                return [
                    'status' => 300,
                    'message' => 'anda tidak berhak login',
                ];
            }

            return [
                'status' => 201,
                'message' => 'Anda berhasil login',
                'link' => 'admin/dashboard',
            ];
        }else{

            return [
                'status' => 300,
                'message' => 'Username atau password anda salah silahkan coba lagi'
            ];
        }
    }

    public function change_password(){
        return view('auth.change-password');
    }

    public function action_change_password(request $request){
        $validator = Validator::make($request->all(), [
			'old_password' => 'required',
			'new_password' => 'required| min:6',
			'retype_password' => 'required|same:new_password',
		]);

		if($validator->fails()) {
			return [
				'status' => 300,
				'message' => $validator->errors()->first()
			];
		}

		$user = User::where('id',Auth::user()->id)->first();

		if (password_verify($request->old_password, $user['password'])) {
			$user->password = Hash::make($request->new_password);
			$user->save();

			return [
				'status' => 200,
				'message' => 'Password berhasil diganti'
			];

		}else{
			return [
				'status' => 300,
				'message' => 'Password lama anda salah, silahkan coba lagi'
			];
		}


    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
