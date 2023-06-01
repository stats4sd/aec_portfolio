<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrincipleAssessmentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rating' => ['nullable', 'numeric', 'lte:2', 'gte:0'],
            'rating_comment' => ['nullable', 'string'],
            'is_na' => ['nullable', 'boolean'],
//            'score_tags' => ['nullable', 'array'],
//            'custom_score_tags' => ['nullable', 'array'],
        ];
    }

}
