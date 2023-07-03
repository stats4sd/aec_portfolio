<?php

namespace App\Http\Requests;

use App\Enums\GeographicalReach;
use App\Rules\UniqueProjectCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'organisation_id' => 'required',
            'portfolio_id' => 'required',
            'name' => 'required|string',
            'code' => ['nullable', 'string', new UniqueProjectCode],
            'description' => 'nullable|string',
            'budget' => 'required|integer|gte:0',
            'currency' => 'required|max:3',
            'exchange_rate' => 'required|numeric',
            'uses_only_own_funds' => 'required|boolean',
            'main_recipient_id' => 'nullable',
            'main_recipient' => 'required',
            'start_date' => 'required',
            'end_date' => 'nullable|after:start_date',
            'geographic_reach' => ['required', Rule::in(collect(GeographicalReach::cases())->pluck('name')->toArray())],
            'continents' => 'required',
            'regions' => 'required',
            'countries' => 'required',
            'sub_regions' => 'nullable'
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
