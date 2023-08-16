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
            'alamat' => 'required',
            'alamat_toko' => 'required',
            'type_pelanggan' => 'required',
            'sales_pelanggan_id' => 'required',
            'email' => [
                Rule::when(request()->isMethod('POST'), 'required|email|unique:users,email,' . $this->id),
                Rule::when(request()->isMethod('PUT'), 'nullable|email'),
            ],
            'telepon' => 'required|digits_between:10,12',
            'password' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
                'min:6',
            ],
            'retype_password' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
                'same:password'
            ],
            'provinsi' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'kota' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'kelurahan' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'kecamatan' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'kota' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'zip' => [
                Rule::when(request()->isMethod('POST'), 'required'),
                Rule::when(request()->isMethod('PUT'), 'nullable'),
            ],
            'no_ktp' => [
                Rule::when(request()->isMethod('POST'), 'nullable|numeric|unique:users,no_ktp'),
                Rule::when(request()->isMethod('PUT'), 'nullable|numeric'),
            ],
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
