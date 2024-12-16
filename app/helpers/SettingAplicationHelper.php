<?php

use App\Models\SettingAplication;
use Illuminate\Support\Carbon;

// Function to get a specific setting value by key (e.g., 'name', 'logo', etc.)
function Setting($key)
{
    // Fetch the first setting record and return the value of the specified key
    return SettingAplication::first()->{$key};
}

// Format value as Rupiah currency
if (!function_exists('formatRupiah')) {
    function formatRupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}

// Format value as a date
if (!function_exists('formatDate')) {
    function formatDate($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}

if (!function_exists('getAppName')) {
    function getAppName()
    {
        return Setting('name');
    }
}

if (!function_exists('getAppLogo')) {
    function getAppLogo()
    {
        return Setting('logo');
    }
}
