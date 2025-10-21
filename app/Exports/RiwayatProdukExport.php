<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $riwayat;
    protected $produk;
    protected $filters;

    public function __construct($riwayat, $produk, $filters)
    {
        $this->riwayat = $riwayat;
        $this->produk = $produk;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->riwayat;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN RIWAYAT PRODUK'],
            ['Nama Produk: ' . ($this->produk->judul ?? '-')],
            ['Periode: ' . ($this->filters['tanggal_awal'] ?? '-') . ' s/d ' . ($this->filters['tanggal_akhir'] ?? '-')],
            [],
            ['Tanggal', 'Jam', 'Tipe', 'Quantity', 'User', 'Keterangan']
        ];
    }

    public function map($row): array
    {
        return [
            date('d-m-Y', strtotime($row->created_at)),
            date('H:i:s', strtotime($row->created_at)),
            $row->type,
            $row->qty,
            $row->user,
            $row->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
        ];
    }
}
