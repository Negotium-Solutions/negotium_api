<?php

namespace App\Helpers;

class Helper
{
    /**
     * @param $phoneNumber
     * @param $prefix
     * @return mixed|string
     */
    public static function replaceNumberPrefix($phoneNumber, $prefix) {
        // Check if the phone number starts with '0'
        if (substr($phoneNumber, 0, 1) === '0') {
            // Replace the first '0' with '27'
            return $prefix . substr($phoneNumber, 1);
        }
        return $phoneNumber; // Return the number as is if it doesn't start with '0'
    }
}
