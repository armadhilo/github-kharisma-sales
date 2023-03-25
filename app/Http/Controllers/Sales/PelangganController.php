<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Helpers\ApiFormatter;
use App\Http\Requests\PelangganRequest;

use App\Models\User;

class PelangganController extends Controller
{
    public function index(){
        dd('aa');
    }

    public function store(PelangganRequest $request){

        $request->merge(['password' => Hash::make($request->password) ]);

        User::create($request->all());

        $message = 'User created successfully';
        return ApiFormatter::success(200, $message);
    }

    public function detail($id){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $message = 'User loaded successfully';
        return ApiFormatter::success(200, $message, $user);
    }

    public function update($id, PelangganRequest $request){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $user->fill($request->all())->save();
        $message = 'User Updated Successfully';
        return ApiFormatter::success(200, $message);
    }

    public function delete($id){

        $user = User::where(['id' => $id, 'type' => '2'])->first();
        if(empty($user)){
            $message = 'User not found';
            return ApiFormatter::error(404, $message);
        }

        $user->delete();
        $message = 'User Deleted Successfully';
        return ApiFormatter::success(200, $message);

    }
}
