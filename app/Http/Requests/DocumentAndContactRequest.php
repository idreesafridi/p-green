<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentAndContactRequest extends FormRequest
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
        $rules['document_number'] = 'required';
        $rules['issued_by'] = 'required';
        $rules['release_date'] = 'required';
        $rules['expiration_date'] = 'required';
        $rules['fiscal_document_number'] = 'required';
        $rules['vat_number'] = 'required';
        $rules['contact_email'] = 'required';
        $rules['contact_number'] = 'required';
        $rules['alt_refrence_name'] = 'required';
        $rules['alt_contact_number'] = 'required';

        return $rules;
    }
}
