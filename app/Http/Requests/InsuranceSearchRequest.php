<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InsuranceSearchRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'zip_code' => 'required|string',
            'insurance' => 'required|string',
            'gender' => 'required|string',
            'married' => 'required|boolean',
            'home_owner' => 'required|boolean',
            'birth_date' => 'required|date',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
            'vehicle.*.year' => 'required|integer',
            'vehicle.*.company' => 'required|string',
            'vehicle.*.model' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'vehicle.*.year' => 'Year field is required.',
            'vehicle.*.company' => 'Company field is required.',
            'vehicle.*.model' => 'Model field is required.'
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