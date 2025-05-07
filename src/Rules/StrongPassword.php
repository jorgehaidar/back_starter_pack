<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $passes = [
            'letters' => '/[a-zA-Z]/',
            'mixed' => preg_match('/(?=.*[a-z])(?=.*[A-Z])/', $value),
            'numbers' => preg_match('/[0-9]/', $value),
            'symbols' => preg_match('/[\W_]/', $value),
            'length' => strlen($value) >= 8,
        ];

        foreach ($passes as $key => $pass) {
            if (!$pass) {
                $fail(__('validation.password.'.$key));
            }
        }
    }
}
