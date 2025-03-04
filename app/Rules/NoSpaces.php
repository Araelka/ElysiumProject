<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSpaces implements ValidationRule
{

    protected $customMessage;

    public function __construct($customMessage = null)
    {
        $this->customMessage = $customMessage;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/\s/', $value)) {
            $fail($this->customMessage ?? "Поле :attribute не должно содержать пробелов.");
        }
    }


    public function message()
    {
        return $this->customMessage ?? 'Поле :attribute не должно содержать пробелов.';
    }
}
