<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SouthAfricanIdNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ensure the ID number is exactly 13 digits long
        if (strlen($value) !== 13 || !ctype_digit($value)) {
            $fail('The :attribute must be exactly 13 digits.');
            return;
        }

        // Extract the date of birth and validate it
        $dob = substr($value, 0, 6);
        $date = \DateTime::createFromFormat('ymd', $dob);

        if (!$date || $date->format('ymd') !== $dob) {
            $fail('The :attribute does not have a valid date of birth.');
            return;
        }

        // Validate the checksum using the Luhn algorithm
        if (!$this->isValidLuhn($value)) {
            $fail('The :attribute is not a valid South African ID number.');
        }
    }

    /**
     * Validate the ID number using the Luhn algorithm.
     *
     * @param string $number
     * @return bool
     */
    protected function isValidLuhn(string $number): bool
    {
        $sum = 0;
        $alt = false;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = (int) $number[$i];

            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }

            $sum += $n;
            $alt = !$alt;
        }

        return ($sum % 10) === 0;
    }
}
