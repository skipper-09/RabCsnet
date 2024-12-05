<?php

use App\Models\SettingAplication;
use Illuminate\Support\Carbon;

function Setting($key){
    return SettingAplication::first()->{$key};
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}


if (!function_exists('formatDate')) {
    function formatDate($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }
}