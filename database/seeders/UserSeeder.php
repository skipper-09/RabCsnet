<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id'=>'b05fab3f-2399-453b-b6f6-bbd2f1054799',
            'name' => 'Developer',
            'username' => 'developer',
            'email' => 'root@gmail.com',
            'password' => Hash::make('root'),
        ])->assignRole('Developer'); 
    }
}
