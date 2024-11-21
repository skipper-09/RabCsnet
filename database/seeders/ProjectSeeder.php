<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'id'=>'e571fb18-ccbb-4424-aae9-00c59e1144ad',
            'name'=>'Project Testing',
            'company_id'=>'3c795b1b-9354-46db-8ad0-156dc52159e4',
            'responsible_person'=>'b05fab3f-2399-453b-b6f6-bbd2f1054799',
            'start_date'=>now(),
            'end_date'=>now(),
            'description'=>'testing',
            'status'=>1,
        ]);
    }
}
