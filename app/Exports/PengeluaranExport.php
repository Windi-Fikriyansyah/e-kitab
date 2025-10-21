<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PengeluaranExport implements FromView
{
    protected $data;
    protected $totalNominal;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $kategori;

    public function __construct($data, $totalNominal, $tanggalAwal, $tanggalAkhir, $kategori)
    {
        $this->data = $data;
        $this->totalNominal = $totalNominal;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->kategori = $kategori;
    }

    public function view(): View
    {
        return view('laporan.pengeluaran.export', [
            'data' => $this->data,
            'totalNominal' => $this->totalNominal,
            'tanggalAwal' => $this->tanggalAwal,
            'tanggalAkhir' => $this->tanggalAkhir,
            'kategori' => $this->kategori
        ]);
    }
}
