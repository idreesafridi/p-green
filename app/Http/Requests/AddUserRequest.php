<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
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
        $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];

        if (request()->user == 'business') {
            $rules['company_name'] = 'required';
            $rules['company_type'] = 'required';
        }

        return $rules;
    }
}
