<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LaporanStokController extends Controller
{
    private function parseKategori($kategori)
    {
        $decoded = json_decode($kategori, true);
        if (is_array($decoded)) {
            return implode(', ', $decoded);
        }
        return $kategori;
    }

    public function index()
    {
        // Data untuk filter mutasi stok
        $barangList = DB::table('produk')
            ->select('id', 'kd_produk', 'judul')
            ->orderBy('judul')
            ->get();

        // Default tanggal (bulan ini)
        $tanggalAwal = date('Y-m-01');
        $tanggalAkhir = date('Y-m-d');

        return view('laporan.stok.index', compact(
            'barangList',
            'tanggalAwal',
            'tanggalAkhir'
        ));
    }

    public function load(Request $request)
    {
        $type = $request->type; // 'tersedia', 'minimum', or 'mutasi'

        if ($type == 'tersedia') {
            $query = DB::table('produk')
                ->select('id', 'kd_produk', 'judul', 'stok', 'kategori');

            return DataTables::of($query)
                ->editColumn('kategori', function ($item) {
                    return $this->parseKategori($item->kategori);
                })
                ->addColumn('status', function ($item) {
                    if ($item->stok < 10) {
                        return '<span class="badge bg-danger">Perlu Restock</span>';
                    } else {
                        return '<span class="badge bg-success">Aman</span>';
                    }
                })
                ->addColumn('stok_minimum', function () {
                    return 10; // Default stok minimum
                })
                ->rawColumns(['status'])
                ->make(true);
        } elseif ($type == 'minimum') {
            $query = DB::table('produk')
                ->select('id', 'kd_produk', 'judul', 'stok', 'kategori')
                ->where('stok', '<', 10);

            return DataTables::of($query)
                ->editColumn('kategori', function ($item) {
                    return $this->parseKategori($item->kategori);
                })
                ->addColumn('stok_minimum', function () {
                    return 10; // Default stok minimum
                })
                ->addColumn('selisih', function ($item) {
                    return 10 - $item->stok;
                })
                ->make(true);
        } elseif ($type == 'mutasi') {
            $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
            $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
            $barangId = $request->barang_id ?? null;

            $startDate = Carbon::parse($tanggalAwal)->startOfDay();
            $endDate = Carbon::parse($tanggalAkhir)->endOfDay();

            // Ambil data barang masuk
            $barangMasuk = DB::table('barang_masuk')
                ->join('produk', 'barang_masuk.id_produk', '=', 'produk.id')
                ->select(
                    'barang_masuk.created_at as tanggal',
                    'produk.kd_produk',
                    'produk.judul',
                    DB::raw("'Masuk' as jenis"),
                    'barang_masuk.stok_masuk as jumlah',
                    'barang_masuk.notes as keterangan'
                )
                ->when($barangId, function ($query) use ($barangId) {
                    return $query->where('barang_masuk.id_produk', $barangId);
                })
                ->whereBetween('barang_masuk.created_at', [$startDate, $endDate])
                ->get();

            // Ambil data barang keluar
            $barangKeluar = DB::table('barang_keluar')
                ->join('produk', 'barang_keluar.id_produk', '=', 'produk.id')
                ->select(
                    'barang_keluar.created_at as tanggal',
                    'produk.kd_produk',
                    'produk.judul',
                    DB::raw("'Keluar' as jenis"),
                    'barang_keluar.stok_keluar as jumlah',
                    'barang_keluar.notes as keterangan'
                )
                ->when($barangId, function ($query) use ($barangId) {
                    return $query->where('barang_keluar.id_produk', $barangId);
                })
                ->whereBetween('barang_keluar.created_at', [$startDate, $endDate])
                ->get();

            // Gabungkan dan urutkan data
            $mutasiData = $barangMasuk->concat($barangKeluar)->sortByDesc('tanggal');

            return DataTables::of($mutasiData)
                ->addColumn('jenis_badge', function ($item) {
                    $class = $item->jenis == 'Masuk' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' . $class . '">' . $item->jenis . '</span>';
                })
                ->editColumn('tanggal', function ($item) {
                    return Carbon::parse($item->tanggal)->format('d/m/Y H:i');
                })
                ->rawColumns(['jenis_badge'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function filter(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'id_produk' => 'nullable|exists:produk,id'
        ]);

        $barangList = DB::table('produk')
            ->select('id', 'kd_produk', 'judul')
            ->orderBy('judul')
            ->get();

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $barangId = $request->id_produk;

        return view('laporan.stok.index', compact(
            'barangList',
            'tanggalAwal',
            'tanggalAkhir',
            'barangId'
        ));
    }
}
