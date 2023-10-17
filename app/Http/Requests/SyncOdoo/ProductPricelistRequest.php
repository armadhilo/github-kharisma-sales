<?php

namespace App\Http\Requests\SyncOdoo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Str;

class ProductPricelistRequest extends FormRequest
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
            'product_tmpl_id' => 'required',
            'product_uom_id' => 'required',
            'price' => 'required',
            'min_quantity' => 'required',
            'pricelist_desc' => 'nullable',
        ];
    }

    protected function passedValidation()
    {
        if($this->has('pricelist_desc')){
            $this->merge(['type' => $this->pricelist_desc]);
            $this->request->remove('pricelist_desc');
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
