<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::create([
            'user_id' => 'b05fab3f-2399-453b-b6f6-bbd2f1054781',
            'name' => 'vendor a',
            'phone' => '787878787',
            'email' => 'vendora@gmail.com',
            'address' => 'vendor alamat'
        ]);
    }
}
