<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = Role::create(['name' => 'Developer']);
        
        $developer->givePermissionTo([
            'read-dashboard',
            'read-users','create-users','update-users','delete-users',
        ]);

        $admin = Role::create(['name' => 'Andministrator']);
        
        $admin->givePermissionTo([
            'read-dashboard',
            'read-users','create-users','update-users','delete-users',
        ]);
    }
}
