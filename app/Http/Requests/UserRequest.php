<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
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
            'email' => 'required|email:rfc,dns',
            'telepon' => 'required|min:10|max:12',
            'access_group' => 'required',
            'alamat' => 'required',
            'password' => [
                Rule::when(is_null(request('id')), 'required|min:6'),
            ],
            'access_menu' => 'required_if:access_group,select access',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = json_decode($validator->errors());
        $array = [];

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
