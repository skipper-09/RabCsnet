<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::insert([
            [
                'id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f45',
                'name' => 'Pcs'
            ],
            ['id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f69', 'name' => 'Meter'],
            ['id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f29', 'name' => 'Core'],
            ['id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f63', 'name' => 'Track'],
            ['id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f21', 'name' => 'Package'],
            ['id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f26', 'name' => 'Lot'],
        ]);
    }
}
