<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProdukTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            // PRODUK
            'judul',
            'cover',
            'kertas',
            'kualitas',
            'harakat',
            'halaman',
            'berat',
            'ukuran',

            'kategori',
            'sub_kategori',

            'penerbit',
            'supplier',
            'penulis',

            'harga_modal',
            'harga_jual',
            'stok',

            'images',
            'link_youtube',
            'editor',
            'deskripsi',

            // PRODUK INDO
            'judul_indo',
            'cover_indo',
            'kertas_indo',
            'kualitas_indo',
            'harakat_indo',

            'kategori_indo',
            'sub_kategori_indo',

            'penerbit_indo',
            'cetakan_indo',
            'penulis_indo',
            'editor_indo',
            'deskripsi_indo',
        ];
    }

    public function array(): array
    {
        return [[
            'Contoh Judul',
            'Cover',
            'HVS 70gr',
            'Premium',
            'Tidak berharakat',
            '120',
            '500 Gram',
            '14x20 cm',

            'Fiqih,Aqidah',
            'Mazhab,Aqidah',

            'Penerbit A',
            '1 (ID Supplier)',
            'Penulis A',

            '30000',
            '50000',
            '10',

            'img1.jpg,img2.jpg',
            'https://youtube.com/…',
            'Editor A',
            'Deskripsi Produk Arab',

            'Contoh Judul Indo',
            'Cover-Indo',
            'HVS 70gr',
            'Premium',
            'Berharakat',

            'Fiqih,Aqidah',
            'Mazhab,Aqidah',

            'Penerbit A',
            'Cetakan 1',
            'Penulis Indo',
            'Editor Indo',
            'Deskripsi Produk Indo'
        ]];
    }
}
