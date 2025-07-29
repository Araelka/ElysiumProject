<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxCharacters implements ValidationRule
{
    protected $max;

    public function __construct($max) {
        $this->max = $max;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cleanedValue = trim(preg_replace('/\s+/', ' ', $value));

        if (mb_strlen($cleanedValue, 'UTF-8') > $this->max) {
            $fail("Максимальная длина поля — {$this->max} символов.");
        }
    }

    public function message() {
        return "Максимальная длина поля — {$this->max} символов.";
    }

}
