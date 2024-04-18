<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConstructionRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [];
        $rules['name'] = 'required';
        $rules['surename'] = 'required';
        $rules['date_of_birth'] = 'required';
        $rules['town_of_birth'] = 'required';
        $rules['province'] = 'required';
        $rules['residence_address'] = 'required';
        $rules['residence_street'] = 'required';
        $rules['residence_zip'] = 'required';
        $rules['residence_common'] = 'required';
        $rules['residence_province'] = 'required';

        return $rules;
    }
}
