<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('tenant_assets')) {
    function tenant_assets($tenant, $path) : string
    {
        return Storage::disk('tenant_assets')->url("$tenant->id/app/$path");
    }
}

if (!function_exists('replaceNumberPrefix')) {
    function replaceNumberPrefix($phoneNumber, $prefix)
    {
        // Check if the phone number starts with '0'
        if (substr($phoneNumber, 0, 1) === '0') {
            // Replace the first '0' with '27'
            return $prefix . substr($phoneNumber, 1);
        }
        return $phoneNumber; // Return the number as is if it doesn't start with '0'
    }
}

if (!function_exists('fieldToLabel')) {
    function fieldToLabel($fieldName)
    {
        // Replace underscores with spaces
        $formatted = str_replace('_', ' ', $fieldName);

        // Capitalize the first letter of each word
        $formatted = ucwords($formatted);

        return $formatted;
    }
}
