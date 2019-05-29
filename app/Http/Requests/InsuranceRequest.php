<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InsuranceRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'make' => 'required|string',
            'year' => 'required|integer'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        if(!$errors->isEmpty()) {
            $response =  [
                'success' => false,
                'error' => implode(', ', $errors->all())
            ];
            throw new HttpResponseException(response()->json($response));
        }
    }
}
