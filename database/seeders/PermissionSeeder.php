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
            'read-projects',
            'create-projects',
            'update-projects',
            'delete-projects',
            'read-detail-projects',
            'approval-projects',
            'start-projects',
            'finish-projects',
            'enable-atp-upload',
            'disable-atp-upload',
            'upload-atp',
            'download-atp',
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
            'read-services',
            'create-services',
            'update-services',
            'delete-services',
            'read-projectreviews',
            'create-projectreviews',
            'update-projectreviews',
            'delete-projectreviews',
            'read-tasks',
            'create-tasks',
            'update-tasks',
            'delete-tasks',
            'complete-tasks',
            'update-kanban-tasks',
            'read-project-timeline',
            'read-reportvendors',
            'create-reportvendors',
            'update-reportvendors',
            'delete-reportvendors',
            'read-report-project',
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
            'setting-aplication',
            'setting-profile',
        ];
        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
