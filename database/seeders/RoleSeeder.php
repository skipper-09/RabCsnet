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
            'read-vendors',
            'create-vendors',
            'update-vendors',
            'delete-vendors',
            'read-paymentvendors',
            'create-paymentvendors',
            'update-paymentvendors',
            'delete-paymentvendors',
            'read-companies',
            'create-companies',
            'update-companies',
            'delete-companies',
            'read-items',
            'create-items',
            'update-items',
            'delete-items',
            'export-items',
            'read-itemtypes',
            'create-itemtypes',
            'update-itemtypes',
            'delete-itemtypes',
            'read-projecttypes',
            'create-projecttypes',
            'update-projecttypes',
            'delete-projecttypes',
            'read-units',
            'create-units',
            'update-units',
            'delete-units',
            'read-projectreviews',
            'create-projectreviews',
            'update-projectreviews',
            'delete-projectreviews',
            'read-tasks',
            'create-tasks',
            'update-tasks',
            'delete-tasks',
            'read-reportvendors',
            'create-reportvendors',
            'update-reportvendors',
            'delete-reportvendors',
            'read-users',
            'create-users',
            'update-users',
            'delete-users',
            'read-roles',
            'create-roles',
            'update-roles',
            'delete-roles',
            'read-logs',
            'clean-logs',
            'read-settings',
        ]);

        $admin = Role::create(['name' => 'Administrator']);

        $admin->givePermissionTo([
            'read-dashboard',
            'read-users',
            'create-users',
            'update-users',
            'delete-users',
        ]);

        $vendor = Role::create(['name' => 'Vendor']);

        $vendor->givePermissionTo([
            'read-dashboard',
            'read-users',
            'create-users',
            'update-users',
            'delete-users',
        ]);

        $accounting = Role::create(['name' => 'Accounting']);

        $accounting->givePermissionTo([
            'read-dashboard',
            'read-projectreviews',
            'create-projectreviews',
            'update-projectreviews',
            'delete-projectreviews',
        ]);

        $owner = Role::create(['name' => 'Owner']);

        $owner->givePermissionTo([
            'read-dashboard',
            'read-projectreviews',
            'create-projectreviews',
            'update-projectreviews',
            'delete-projectreviews',
        ]);
    }
}
