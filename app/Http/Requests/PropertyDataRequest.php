<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyDataRequest extends FormRequest
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
        $rules['property_address'] = 'required';
        $rules['address'] = 'required';
        $rules['house_number'] = 'required';
        $rules['common'] = 'required';
        $rules['zip_code'] = 'required';
        $rules['province'] = 'required';
        $rules['cadastral_section'] = 'required';
        $rules['cadastral_sheet'] = 'required';
        $rules['cadastral_particle'] = 'required';
        $rules['sub_ordinate'] = 'required';
        $rules['pod_codes'] = 'required';

        return $rules;
    }
}
