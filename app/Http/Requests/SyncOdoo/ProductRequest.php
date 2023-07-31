<?php

namespace App\Http\Requests\SyncOdoo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Str;

class ProductRequest extends FormRequest
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
            'product_id' => 'required',
            'default_code' => 'required',
            'product_tmpl_id' => 'nullable',
            'product_uom_id' => 'nullable',
            'product_tag_id' => 'nullable',
            'product_name' => 'nullable',
            'image' => 'nullable',
            'image_medium' => 'nullable',
            'unit' => 'nullable',
            'weight' => 'nullable',
            'qty_available' => 'nullable',
        ];
    }

    protected function passedValidation()
    {
        if($this->has('product_name')){
            $this->merge(['product_name_slug' => Str::slug($this->product_name)]);
        }

        if($this->has('default_code')){
            $this->merge(['sku' => $this->default_code]);
            $this->request->remove('default_code');
        }

        if($this->has('qty_available')){
            $this->merge(['stock' => $this->qty_available]);
            $this->request->remove('qty_available');
        }

        if($this->has('image_medium')){
            $this->merge(['image_small' => $this->image_medium]);
            $this->request->remove('image_medium');
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
