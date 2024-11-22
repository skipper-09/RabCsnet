<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectType::create([
            'name' => 'Distribusi'
        ]);
        ProjectType::create([
            'name' => 'Feeder'
        ]);
        ProjectType::create([
            'name' => 'Backbone'
        ]);
    }
}
