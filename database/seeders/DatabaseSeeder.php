<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            ItemTypeSeeder::class,
            UnitSeeder::class,
            CompanySeeder::class,
            ItemSeeder::class,
            // ProjectSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProjectTypeSeeder::class,
            SettingAplicationSeeder::class,
        ]);
    }
}
