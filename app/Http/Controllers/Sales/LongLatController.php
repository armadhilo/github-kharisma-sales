<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Helpers\ApiFormatter;
use App\Http\Requests\LongLatRequest;
use App\Models\AlamatPengiriman;
use App\Models\User;
use DB;
use Hash;
use Str;

class LongLatController extends Controller
{
    public function update($id, LongLatRequest $request){

        DB::beginTransaction();
        try{

            User::where('id', $id)->update([
                'lat' => $request->lat,
                'lng' => $request->lng,
            ]);

            $user = User::find('id', $id);

            //check if has password
            if(!isset($user->password)){
                $user->password = Hash::make('pelanggan');
                $user->save();
            }

            if((!isset($user->keylog))){
                $user->keylog = Str::random(40);
                $user->save();
            }

            AlamatPengiriman::updateOrCreate(['user_id' => $user->pelanggan_id],[
                'user_id' => $user->pelanggan_id,
                'penerima' => $user->nama_toko,
                'no_telepon' => $user->telepon,
                'alamat' => $user->alamat_toko,
            ]);

            DB::commit();

            $message = 'Pelanggan location updated Successfully';
            return ApiFormatter::success(200, $message);
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
