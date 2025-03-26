<?php

namespace Mbox\BackCore\Rules;

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
        $messages = [
            'uppercase' => 'Must contain at least one uppercase letter.',
            'lowercase' => 'Must contain at least one lowercase letter.',
            'number' => 'Must contain at least one number.',
            'symbol' => 'Must contain at least one symbol.',
            'length' => 'Must be at least 8 characters long.',
        ];

        $passes = [
            'uppercase' => preg_match('/[A-Z]/', $value),
            'lowercase' => preg_match('/[a-z]/', $value),
            'number' => preg_match('/[0-9]/', $value),
            'symbol' => preg_match('/[\W_]/', $value),
            'length' => strlen($value) >= 8,
        ];

        foreach ($passes as $key => $pass) {
            if (!$pass) {
                $fail($messages[$key]);
            }
        }
    }
}
