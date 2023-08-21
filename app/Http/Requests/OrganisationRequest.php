<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrganisationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'currency' => 'required|max:3',
            'has_additional_criteria' => 'sometimes|nullable|bool',
            'institution_type_id' => 'nullable|exists:institution_types,id',
            'institution_type_other' => 'sometimes|nullable',
            'geographic_reach' => 'sometimes|nullable',
            'hq_country' => 'nullable|exists:countries,id',
            'agreement' => 'nullable'
        ];
    }
}
