<?php

namespace Database\Seeders;

use App\Models\SettingAplication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingAplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SettingAplication::create([
            'name' => 'Csnet',
            'logo' => 'logocsnet.png',
            'description' => 'testing'
        ]);
    }
}
