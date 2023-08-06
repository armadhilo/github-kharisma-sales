<?php

namespace App\Http\Controllers\Ecom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Helpers\ApiFormatter;
use App\Http\Requests\Ecom\OngkosKirimRequest;
use App\Models\Config;
use App\Models\JasaPengiriman;

class OngkosKirimController extends Controller
{
    public function index(OngkosKirimRequest $request){
        $asal_id = Config::where('config','ASAL PENGIRIMAN - KECAMATAN ID')->first()->value; //surabaya
        $jasa_pengiriman = JasaPengiriman::active()->where('kode','!=','staff')->where('kode', '!=', 'mandiri')->get();

        //jika berat 0 jadikan 1 gram saja
        if($request->berat == 0){
            $request->merge(['berat' => 1]);
        }

        $array = [];
        foreach($jasa_pengiriman as $item){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pro.rajaongkir.com/api/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "origin=$asal_id&originType=subdistrict&destination=$request->kecamatan_id&destinationType=subdistrict&weight=$request->berat&courier=$item->kode",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key:7021b582cede2d3c37dbe64f18d6c29b"
            ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            if(!empty($err)){
                $message = 'Terjadi masalah pada server pengiriman, coba lagi nanti';
                return ApiFormatter::error(404, $message);
            }

            curl_close($curl);

            $response = json_decode($response, true);
            $data = collect($response['rajaongkir']['results']);

            if(count($data)){
                foreach($data[0]['costs'] as $value){
                    $array[] = [
                        'nama' => "[".strtoupper($data[0]['code']) ."] ". $value['service'],
                        'harga' => $value['cost'][0]['value'],
                        'estimasi' => $value['cost'][0]['etd'],
                    ];
                }
            }
        }

        //KURIR KHARISMA
        // kecamatan dalam kota kupang 2998, 2999, 3000, 3001, 3002, 3003
        // kecamatan soe [479] = 6611
        // kecamatan Kefamenanu [480] = 6643
        // kecamatan attambua [58] = 773, 774, 775

        if($request->kecamatan_id == '2998' || $request->kecamatan_id == '2999' || $request->kecamatan_id == '3000' || $request->kecamatan_id == '3001' ||
            $request->kecamatan_id == '3002' || $request->kecamatan_id == '3003' || $request->kecamatan_id == '6611' || $request->kecamatan_id == '6643' ||
            $request->kecamatan_id == '773' || $request->kecamatan_id == '774' || $request->kecamatan_id == '775'){

            $kurir_kharisma = JasaPengiriman::active()->where('kode', 'staff')->first();
            if(!empty($kurir_kharisma)){

                //disable karena harusnya sudah tidak ada ongkos
                // if($request->berat <= 2000){
                //     $harga = 10000;
                // }else{
                //     $berat_kharisma = $request->berat - 2000;
                //     $berat_kharisma = round($berat_kharisma, -3);
                //     $berat_kharisma = $berat_kharisma / 1000;

                //     $harga = 10000 + ($berat_kharisma * 10000);
                // }

                $array[] = [
                    'nama' => "[STAF KHARISMA] - KHUSUS KOTA KUPANG",
                    'harga' => 0,
                    'estimasi' => "1",
                ];
            }
        }

        // AMBIL SENDIRI
        // kecamatan dalam kota kupang 2998, 2999, 3000, 3001, 3002, 3003
        if($request->kecamatan_id == '2998' || $request->kecamatan_id == '2999' || $request->kecamatan_id == '3000' || $request->kecamatan_id == '3001' ||$request->kecamatan_id == '3002' || $request->kecamatan_id == '3003'){
            $mandiri = JasaPengiriman::active()->where('kode', 'mandiri')->first();
            if(!empty($mandiri)){

                $array[] = [
                    'nama' => "AMBIL DI TOKO",
                    'harga' => 0,
                    'estimasi' => "-",
                ];
            }
        }

        $message = 'Ongkos kirim berhasil ditampilkan';
        return ApiFormatter::success(200, $message, $array);
    }
}
