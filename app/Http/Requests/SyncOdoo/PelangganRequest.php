<?php

namespace App\Http\Requests\SyncOdoo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Str;

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
            'pelanggan_id' => 'required',
            'property_product_pricelist' => 'nullable',
            'npwp' => 'nullable',
            'npwp_name' => 'nullable',
            'id_nik' => 'nullable',
            'email' => 'nullable',
            'name' => 'nullable',
            'display_name' => 'nullable',
            'mobile' => 'nullable',
            'street' => 'nullable',
            'street2' => 'nullable',
            'city' => 'nullable',
            'zip' => 'nullable',
        ];
    }

    protected function passedValidation()
    {
        if($this->has('property_product_pricelist')){
            $this->merge(['type_pelanggan' => $this->property_product_pricelist]);
            $this->request->remove('property_product_pricelist');
        }

        if($this->has('name')){
            $this->merge(['nama' => $this->name]);
            $this->request->remove('name');
        }

        if($this->has('npwp')){
            $this->merge(['no_npwp' => $this->npwp]);
            $this->request->remove('npwp');
        }

        if($this->has('npwp_name')){
            $this->merge(['nama_npwp' => $this->npwp_name]);
            $this->request->remove('npwp_name');
        }

        if($this->has('display_name')){
            $this->merge(['nama_toko' => $this->display_name]);
            $this->request->remove('display_name');
        }

        if($this->has('id_nik')){
            $this->merge(['no_ktp' => $this->id_nik]);
            $this->request->remove('id_nik');
        }

        if($this->has('mobile')){
            $this->merge(['telepon' => $this->mobile]);
            $this->request->remove('mobile');
        }
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
