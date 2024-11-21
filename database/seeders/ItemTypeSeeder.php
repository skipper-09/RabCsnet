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
            ['id'=>'26dc6575-6dfe-41a1-a3ac-b08bcb632473','name'=>'Cable FTTH'],
            ['id'=>'26dc6575-6dfe-41a1-a3ac-b08bcb632480','name'=>'Perangkat FTTH'],
        ]);
    }
}
