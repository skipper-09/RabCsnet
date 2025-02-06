<?php

namespace App\Exports;

use App\Models\Item;


class ItemExportPdf
{
    /**
     * Export the items to PDF.
     *
     * @return \Barryvdh\DomPDF\PDF
     */
    public function exportToPdf()
    {
        $items = Item::with(['unit', 'type'])->get()->map(function ($item, $index) {
            return [
                'No' => $index + 1,
                'Nama' => $item->name,
                'Tipe' => $item->type->name,
                'Uraian' => $item->description,
                'Unit' => $item->unit->name,
                'Harga Material' => number_format($item->material_price, 0, '', '.'), 
                'Harga Jasa' => number_format($item->service_price, 0, '', '.'), 
            ];
        });

        // Return a PDF response with a view and the data
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.master.item.itempdf', ['items' => $items]);

        // Optionally, you can download the PDF:
        // return $pdf->download('items.pdf');

        // Or you can stream it to the browser:
        return $pdf->stream('items.pdf');
    }
}

