<?php

use App\Models\SettingAplication;

function Setting($key){
    return SettingAplication::first()->{$key};
}