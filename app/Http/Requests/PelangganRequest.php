<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Helpers\ApiFormatter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PelangganRequest extends FormRequest
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
            'nama' => 'required',
            'nama_toko' => 'required',
            'type_pelanggan' => 'required',
            'sales_pelanggan_id' => 'required',
            'email' => [
                Rule::when(request()->isMethod('POST'), 'required|email|unique:users,email,' . $this->id),
            ],
            'telepon' => 'required|digits_between:10,12',
            'password' => [
                'nullable',
                'min:6',
            ],
            'kota_id' => [
                'required',
            ],
            'kecamatan_id' => [
                'required',
            ],
            'kelurahan' => [
                'required',
            ],
            'zip' => [
                'required',
            ],
            'no_ktp' => 'nullable|numeric',
            'nama_npwp' => 'nullable',
            'no_npwp' => 'nullable|numeric',
            'lat' => 'nullable',
            'lng' => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        if($this->password == null){
            $this->request->remove('password');
        }

        if($this->retype_password == null){
            $this->request->remove('retype_password');
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
