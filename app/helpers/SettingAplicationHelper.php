<?php

use App\Models\SettingAplication;

function Setting($key){
    return SettingAplication::first()->{$key};
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}