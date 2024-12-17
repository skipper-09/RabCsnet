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
        return Item::with(['unit', 'type'])->get()->map(function ($item) {
            return [
                'Nama'=>$item->name,
                'Tipe'=>$item->type->name,
                'Kode'=>$item->item_code,
                'Unit'=>$item->unit->name,
                'Harga Material'=>$item->material_price,
                // 'Harga Jasa'=>$item->service_price,
                'Deskripsi'=>$item->description,
                'Status'=>$item->status == 1 ? 'Aktif' : "Tidak Aktif"
            ];
        });
        ;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tipe',
            'Kode',
            'Unit',
            'Harga Material',
            // 'Harga Jasa',
            'Deskripsi',
            'Status'
        ];
    }
}
