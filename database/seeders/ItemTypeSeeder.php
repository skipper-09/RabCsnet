<?php

namespace Database\Seeders;

use App\Models\TypeItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeItem::insert([
            ['name'=>'Cable FTTH'],
            ['name'=>'Perangkat FTTH'],
        ]);
    }
}
