<?php

namespace App\Http\Requests\SyncOdoo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Str;

class ProductUomRequest extends FormRequest
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
            'uom_desc' => 'required',
            'pricelist_desc' => 'required',
        ];
    }

    protected function passedValidation()
    {
        if($this->has('uom_desc')){
            $this->merge(['label' => $this->uom_desc]);

            $stock = 1;
            if (str_contains($this->label, '/')) {
                $explode_stock = explode("/",$this->label);
                $stock = $explode_stock[1];
            }
            $this->merge(['stock' => $stock]);
            $this->request->remove('uom_desc');
        }

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
