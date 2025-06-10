<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class DisplayBudgetRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $displayBudget = $value;

        $budget = Str::replace(',', '', $displayBudget);
        $budget = Str::replace('.', '', $budget);

        if (!ctype_digit($budget)) {
            $fail('The budget must be a number.');
        }
    }
}
