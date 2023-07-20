<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibraryPro\Rules\Concerns\ValidatesMedia;

class UserFeedbackRequest extends FormRequest
{

    use ValidatesMedia;

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
            'user_id' => 'nullable|exists:users,id',
            'user_feedback_type_id' => 'required|exists:user_feedback_types,id',
            'message' => 'required|max:65535',
            'uploads' => ['sometimes', 'nullable', $this
                ->validateMultipleMedia()
                ->maxItems(5)
                ->maxItemSizeInKb(10000)
            ],
        ];
    }
}
