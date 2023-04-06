<?php

namespace App\Http\Requests\Ecom;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AlamatPengirimanRequest extends FormRequest
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
        return [
            'penerima' => 'required',
            'no_telepon' => 'required',
            'provinsi' => 'required',
            'kota' => 'required',
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required',
            'alamat' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator) {

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
