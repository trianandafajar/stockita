<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithEvents
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $query = Product::with(['category', 'warehouse', 'store']);

        if ($this->user->hasRole('admin')) {
            return $query->get();
        }
        if ($this->user->hasRole('owner')) {
            return $query->where('store_id', $this->user->store->id)->get();
        }

        return collect();
    }

    public function headings(): array
    {
        return [
            'No',
            'Gambar Produk',
            'Nama Produk',
            'SKU',
            'Harga',
            'Kategori',
            'Gudang',
            'Status Aktif',
            'Toko',
            'Tanggal Dibuat',
        ];
    }

    public function map($product): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            '',
            $product->name,
            $product->sku,
            $product->price,
            $product->category?->name ?? '-',
            $product->warehouse_id,
            $product->is_active ? 'Aktif' : 'Non-Aktif',
            $product->store?->name ?? '-',
            $product->created_at->format('d-m-Y')
        ];
    }

    public function drawings()
    {
        $drawings = [];

        $products = $this->collection();

        foreach ($products as $index => $product) {
            $path = storage_path('app/public/' . $product->image);

            if ($product->image && file_exists($path)) {
                $drawing = new Drawing();
                $drawing->setName($product->name);
                $drawing->setPath($path);
                $drawing->setHeight(50);

                $column = 'B';
                $row = $index + 2;
                $drawing->setCoordinates($column . $row);

                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);

                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                for ($i = 2; $i <= $highestRow; $i++) {
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(45);
                }
            },
        ];
    }
}
