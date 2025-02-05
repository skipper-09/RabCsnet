<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Item::with(['unit', 'type'])->get()->map(function ($item,$index) {
            return [
                'No' =>$index + 1,
                'Nama'=>$item->name,
                'Tipe'=>$item->type->name,
                'uraian'=>$item->description,
                'Unit'=>$item->unit->name,
                'Harga Material'=>$item->material_price,
                'Harga Jasa'=>$item->service_price,
            ];
        });
        ;
    }

    public function headings(): array
    {
        return [
            'N0',
            'ITEM DESIGN',
            'TIPE',
            'URAIAN DESIGN',
            'SATUAN',
            'MATERIAL',
            'JASA',
        ];
    }
}
