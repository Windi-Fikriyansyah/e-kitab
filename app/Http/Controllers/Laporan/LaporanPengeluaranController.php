<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Exports\PengeluaranExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPengeluaranController extends Controller
{
    public function index()
    {
        $kategoriList = DB::table('pengeluaran')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->get();

        return view('laporan.pengeluaran.index', compact('kategoriList'));
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
            $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
            $kategori = $request->kategori;

            // === Query pengeluaran ===
            $query = DB::table('pengeluaran')
                ->select('tanggal', 'kategori', 'deskripsi', 'nominal', 'metode_bayar', 'lampiran')
                ->whereBetween('tanggal', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ])
                ->orderByDesc('tanggal');

            if (!empty($kategori)) {
                $query->where('kategori', $kategori);
            }

            $data = $query->get();
            $totalPengeluaran = $data->sum('nominal');

            // === Hitung total pemasukan dari tabel transaksi ===
            $totalPemasukan = DB::table('transaksi')
                ->whereBetween('created_at', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ])
                ->where('payment_status', 'lunas') // hanya yang sudah dibayar
                ->sum('total');


            // === Hitung laba rugi ===
            $labaRugi = $totalPemasukan - $totalPengeluaran;

            return DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    'total_nominal' => $totalPengeluaran,
                    'total_pemasukan' => $totalPemasukan,
                    'laba_rugi' => $labaRugi
                ])
                ->make(true);
        }
    }


    public function exportExcel(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
        $kategori = $request->kategori;

        $query = DB::table('pengeluaran')
            ->select('tanggal', 'kategori', 'deskripsi', 'nominal', 'metode_bayar', 'lampiran')
            ->whereBetween('tanggal', [
                Carbon::parse($tanggalAwal)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ])
            ->orderBy('tanggal');

        if (!empty($kategori)) {
            $query->where('kategori', $kategori);
        }

        $data = $query->get();
        $totalNominal = $data->sum('nominal');

        $fileName = 'Laporan_Pengeluaran_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new PengeluaranExport($data, $totalNominal, $tanggalAwal, $tanggalAkhir, $kategori),
            $fileName
        );
    }
}
