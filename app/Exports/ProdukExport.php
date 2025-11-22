<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdukExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                $item->id,
                $item->kd_produk,
                $item->judul,
                $item->cover,
                $item->kertas,
                $item->kualitas,
                $item->harakat,
                $item->halaman,
                $item->berat,
                $item->ukuran,
                $item->kategori,
                $item->sub_kategori,
                $item->penerbit,
                $item->supplier,
                $item->penulis,
                $item->harga_modal,
                $item->harga_jual,
                $item->laba,
                $item->stok,
                $item->images,
                $item->link_youtube,
                $item->editor,
                $item->deskripsi,
                $item->created_at,
                $item->updated_at
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'KD Produk',
            'Judul',
            'Cover',
            'Kertas',
            'Kualitas',
            'Harakat',
            'Halaman',
            'Berat',
            'Ukuran',
            'Kategori',
            'Sub Kategori',
            'Penerbit',
            'Supplier',
            'Penulis',
            'Harga Modal',
            'Harga Jual',
            'Laba',
            'Stok',
            'Images',
            'Link YouTube',
            'Editor',
            'Deskripsi',
            'Created At',
            'Updated At'
        ];
    }
}
