<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create(
            [
                'id'=>'5ed9c28e-eb39-421a-95b9-aa22de564045',
                'name' => 'Pemasangan Kabel Fiber Optik',
                'price' => 1000000,
            ]
        );
    }
}
