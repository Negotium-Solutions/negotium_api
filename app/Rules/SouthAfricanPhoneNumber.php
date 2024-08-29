<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SouthAfricanPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove any spaces or non-numeric characters except '+'
        $cleanedNumber = preg_replace('/[^\d+]/', '', $value);

        // Remove the +27 prefix if present
        if (strpos($cleanedNumber, '+27') === 0) {
            $cleanedNumber = '0' . substr($cleanedNumber, 3); // Remove the '+27' prefix
        } elseif (strpos($cleanedNumber, '27') === 0) {
            // Handle cases where the number starts with '27' instead of '+27'
            $cleanedNumber = '0' . substr($cleanedNumber, 2); // Remove the '27' prefix
        }

        // Ensure the phone number is at least 10 digits long
        if (strlen($value) !== 10 || !ctype_digit($value)) {
            $fail('The :attribute must have at least 10 digits.');
            return;
        }

        if (preg_match('/^0[1-9][0-9]\d{7}$/', $cleanedNumber)) {
            $fail('The :attribute must have a valid landline number.');
            return;
        }

        if (preg_match('/^0[6-9]\d{9}$/', $cleanedNumber)) {
            $fail('The :attribute must have a valid mobile number.');
            return;
        }
    }
}
