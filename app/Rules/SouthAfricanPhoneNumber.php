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
        $cleanedNumber = preg_replace('/[^\d+]/', '', $value);

        if (str_starts_with($cleanedNumber, '+27')) {
            $cleanedNumber = '0' . substr($cleanedNumber, 3);
        } elseif (str_starts_with($cleanedNumber, '27')) {
            $cleanedNumber = '0' . substr($cleanedNumber, 2);
        }

        if (strlen($cleanedNumber) !== 10 || !ctype_digit($cleanedNumber)) {
            $fail('The :attribute must have exactly 10 digits.');
            return;
        }

        if ($attribute === 'sa_phone_number') {
            if (!preg_match('/^0[1-9][0-9]\d{7}$/', $cleanedNumber)) {
                $fail('The :attribute must be a valid South African phone/landline number.');
                return;
            }
        }

        if ($attribute !== 'sa_mobile_number') {
            if (!preg_match('/^0[6-9]\d{9}$/', $cleanedNumber)) {
                $fail('The :attribute must be a valid South African mobile/cellphone number.');
                return;
            }
        }
    }
}
