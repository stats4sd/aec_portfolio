<?php

namespace App\Http\Requests;

use App\Enums\GeographicalReach;
use App\Rules\DisplayBudgetRule;
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
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', new UniqueProjectCode, 'max:255'],
            'description' => 'nullable|string|max:5000',
            'initiativeCategory' => 'required|exists:initiative_categories,id',
            'initiative_category_other' => 'nullable',
            'budget' => 'required|integer|gte:0|lte:2147483647',

            // displayBudget is required for manual adding initiative in front-end,
            // it is not necessary for project import
            // comment it temporary for testing
            // 'displayBudget' => ['required', new DisplayBudgetRule],
            'fundingSources.*.source' => 'exclude_unless:uses_only_own_funds,1|required_without:fundingSources.*.institution_id|max:255',
            'fundingSources.*.institution_id' => 'exclude_unless:uses_only_own_funds,1|required_without:fundingSources.*.source',
            'fundingSources.*.amount' => 'exclude_unless:uses_only_own_funds,1|required|integer|gte:0|lte:2147483647',

            'currency' => 'required|max:3',
            'exchange_rate' => 'sometimes|required|gte:0|lte:2147483647',
            'exchange_rate_eur' => 'sometimes|required|gte:0|lte:2147483647',
            'uses_only_own_funds' => 'required|boolean',
            'main_recipient_id' => 'nullable',
            'main_recipient' => 'required|max:5000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|after:start_date|date',
            'geographic_reach' => ['required', Rule::in(collect(GeographicalReach::cases())->pluck('value')->toArray())],
            'continents' => 'required',
            'regions' => 'required_if:has_all_regions,0',
            'countries' => 'required_if:has_all_countries,0',
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
            'displayBudget.required' => 'The budget field is required.',
            'regions.required_if' => 'The regions field is required if "has all regions" is not ticked.',
            'countries.required_if' => 'The countries field is required if "has all countries" is not ticked.',
            'fundingSources.*.source.required_without' => 'For each funding source, please either select a source from the dropdown list or type in the name of the source.',
            'fundingSources.*.institution_id.required_without' => 'For each funding source, please either select a source from the dropdown list or type in the name of the source.',
            'fundingSources.*.amount' => 'Please enter a valid amount for all funding sources listed',
        ];
    }
}
