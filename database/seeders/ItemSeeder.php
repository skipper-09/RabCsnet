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
                'name' => 'AC-OF-SM-12C',
                'type_id' => 1,
                'unit_id' => 1,
                'material_price' => 9000,
                'service_price' => 4100,
                'description' => 'Cable Aerial Fiber Optik Single Mode 12 Core G.652 D',
            ]
        );
    }
}
