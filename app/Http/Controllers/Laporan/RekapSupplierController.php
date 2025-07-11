<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class RekapSupplierController extends Controller
{
    public function index()
    {
        $id_supplier = auth()->user()->id_supplier;
        $products = DB::table('produk')
            ->where('supplier', $id_supplier)
            ->get();

        return view('laporan.rekapSupplier.index', compact('products'));
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
            $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
            $produk = $request->produk;
            $id_supplier = auth()->user()->id_supplier;

            // Query untuk data yang akan ditampilkan di tabel
            $query = DB::table('transaksi_items')
                ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
                ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->select(
                    'produk.kd_produk',
                    'produk.judul',
                    'supplier.nama_supplier',
                    'produk.harga_modal',
                    DB::raw('SUM(transaksi_items.quantity) as qty_terjual'),
                    DB::raw('SUM(transaksi_items.quantity * produk.harga_modal) as total_terjual'),
                    DB::raw('MAX(transaksi.created_at) as last_transaction')
                )
                ->where('supplier.id', $id_supplier)
                ->whereBetween('transaksi.created_at', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ]);

            if (!empty($produk)) {
                $query->where('produk.kd_produk', $produk);
            }

            $data = $query->groupBy('produk.kd_produk', 'produk.judul', 'supplier.nama_supplier', 'produk.harga_modal')
                ->get();

            // Query terpisah untuk menghitung total (tanpa grouping yang ketat)
            $totalQuery = DB::table('transaksi_items')
                ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
                ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->where('supplier.id', $id_supplier)
                ->whereBetween('transaksi.created_at', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ]);

            if (!empty($produk)) {
                $totalQuery->where('produk.kd_produk', $produk);
            }

            $totalQty = $totalQuery->sum('transaksi_items.quantity');
            $totalNilai = $totalQuery->sum(DB::raw('transaksi_items.quantity * produk.harga_modal'));

            return DataTables::of($data)
                ->addIndexColumn()
                ->with([
                    'total_qty' => $totalQty,
                    'total_nilai' => $totalNilai
                ])
                ->make(true);
        }
    }

    public function exportExcel(Request $request)
    {
        // Implementasi export Excel
    }
}
