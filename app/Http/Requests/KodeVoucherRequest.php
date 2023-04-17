<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Helpers\Convert;

class KodeVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    
    public function rules()
    {
        $array_validation = [
            'tipe' => 'required',
            'kode_voucher' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'kode_voucher' => 'required',
            'is_claim' => 'required',
            'maksimal_penggunaan' => 'required',
            'keterangan' => 'required',
        ];

        if($this->request->get('tipe') == 'persen'){
            $array_validation += [
                'persen_presentase_potongan' => 'required',
                'persen_minimal_pembelian' => 'required',
                'persen_maksimal_potongan' => 'required',
            ];
        } else {
            $array_validation  += [
                'master_produk_id_beli' => 'required',
                'minimal_produk_beli' => 'required',
                'master_produk_id_bonus' => 'required',
                'jumlah_produk_bonus' => 'required',
            ];
        }

        if($this->request->get('voucher_spesifikasi_user') == 'Y'){
            $array_validation  += [
                'user_id' => 'required',
            ];
        }

        return $array_validation;
    }

    protected function prepareForValidation()
    {
        if($this->request->get('voucher_spesifikasi_user') == 'N'){
            $this->request->remove('user_id');
        }

        if($this->request->get('tipe') == 'persen'){
            $this->merge([
                'persen_minimal_pembelian' => Convert::convert_to_double($this->request->get('persen_minimal_pembelian')),
                'persen_maksimal_potongan' => Convert::convert_to_double($this->request->get('persen_maksimal_potongan')),
                'master_produk_id_beli' => null,
                'minimal_produk_beli' => null,
                'master_produk_id_bonus' => null,
                'jumlah_produk_bonus' => null,
            ]);
        } else {
            $this->merge([
                'persen_presentase_potongan' => null,
                'persen_minimal_pembelian' => null,
                'persen_maksimal_potongan' => null,
            ]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = json_decode($validator->errors());
        $array = [];

        //format error validation message laravel to Wowrack RESTAPI format
        foreach($errors as $key => $item){
            foreach($item as $error){
                $array[] = [
                    'message' => $error,
                    'field' => $key,
                ];
            }
        }

        throw new HttpResponseException(response()->json([
            'code' => 400,
            'errors' => $array,
            'message' => 'Input validation error'
        ], 400));
    }
}
