<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayOfPermissionNames = [
            'read-dashboard',
            'read-users','create-users','update-users','delete-users',
            'read-roles','create-roles','update-roles','delete-roles',
        ];
        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });
        
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
    
}
