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
        Project::insert([
            'name'=>'Project Testing',
            'company_id'=>1,
            'responsible_person'=>1,
            'start_date'=>now(),
            'end_date'=>now(),
            'description'=>'testing',
            'status'=>1,
        ]);
    }
}
