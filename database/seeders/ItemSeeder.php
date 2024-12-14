<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create(
            [
                'id'=>'26dc6575-6dfe-41a1-a3ac-b08bcb632480',
                'name' => 'AC-OF-SM-12C',
                'type_id' => '26dc6575-6dfe-41a1-a3ac-b08bcb632480',
                'unit_id' => 'b051e8ea-a0f8-4483-93c9-6352b72f8f45',
                'material_price' => 9000,
                // 'service_price' => 4100,
                'description' => 'Cable Aerial Fiber Optik Single Mode 12 Core G.652 D',
            ]
        );
    }
}
