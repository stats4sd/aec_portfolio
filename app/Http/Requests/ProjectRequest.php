<?php

namespace App\Http\Requests;

use App\Rules\UniqueProjectCode;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'code' => ['nullable', 'string', new UniqueProjectCode],
            'description' => 'nullable|string',
            'budget' => 'required|integer',
            'currency' => 'required|max:3',
            'start_date' => 'nullable',
            'end_date' => 'nullable|after:start_date',
            'global' => 'nullable',
            'countries' => 'nullable',
            'regions' => 'nullable'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
