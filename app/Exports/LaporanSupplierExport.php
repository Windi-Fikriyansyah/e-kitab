<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanSupplierExport implements FromView, ShouldAutoSize
{
    public function __construct(
        public $rowsTransaksi,
        public $rowsTransaksiSupplier,
        public array $meta
    ) {}

    public function view(): View
    {
        return view('laporan.supplier.export_excel', [
            'rowsTransaksi' => $this->rowsTransaksi,
            'rowsTransaksiSupplier' => $this->rowsTransaksiSupplier,
            'meta' => $this->meta,
        ]);
    }
}
