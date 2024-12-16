<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id'=>'b05fab3f-2399-453b-b6f6-bbd2f1054799',
            'picture' => Faker::create()->imageUrl(300, 300),
            'name' => 'Developer',
            'username' => 'developer',
            'email' => 'root@gmail.com',
            'password' => Hash::make('root'),
        ])->assignRole('Developer'); 

        User::create([
            'id'=>'b05fab3f-2399-453b-b6f6-bbd2f1054781',
            'picture' => Faker::create()->imageUrl(300, 300),
            'name' => 'Vendor',
            'username' => 'vendor',
            'email' => 'vendor@gmail.com',
            'password' => Hash::make('vendor'),
        ])->assignRole('Vendor'); 

        User::create([
            'id'=>'472d06b8-e7b3-444a-826d-23b1664575f0',
            'picture' => Faker::create()->imageUrl(300, 300),
            'name' => 'Accounting',
            'username' => 'accounting',
            'email' => 'accounting@gmail.com',
            'password' => Hash::make('accounting'),
        ])->assignRole('Accounting');

        User::create([
            'id'=>'e64d2178-5c22-4aea-b400-db9838941a18',
            'picture' => Faker::create()->imageUrl(300, 300),
            'name' => 'Ko Xiang',
            'username' => 'owner',
            'email' => 'owner@gmail.com',
            'password' => Hash::make('owner'),
        ])->assignRole('Owner');
    }
}
