<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LaporanSupplierController extends Controller
{
    public function index()
    {
        $suppliers = DB::table('supplier')->get();
        $products = DB::table('produk')->get();

        return view('laporan.supplier.index', compact('suppliers', 'products'));
    }

    public function load(Request $request)
    {
        if ($request->ajax()) {
            $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
            $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
            $supplier = $request->supplier;
            $produk = $request->produk;

            // Main query for datatable
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
                    'transaksi.created_at'
                )
                ->whereBetween('transaksi.created_at', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay()
                ]);

            if (!empty($supplier)) {
                $query->where('supplier.id', $supplier);
            }

            if (!empty($produk)) {
                $query->where('produk.kd_produk', $produk);
            }

            $data = $query->groupBy('produk.kd_produk', 'produk.judul', 'supplier.nama_supplier', 'produk.harga_modal', 'transaksi.created_at')
                ->get();

            // Calculate totals
            $totalQty = $data->sum('qty_terjual');
            $totalNilai = $data->sum('total_terjual');

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
        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-01');
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
        $supplier = $request->supplier;
        $produk = $request->produk;

        $data = DB::table('transaksi_items')
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
                'transaksi.created_at'
            )
            ->whereBetween('transaksi.created_at', [
                Carbon::parse($tanggalAwal)->startOfDay(),
                Carbon::parse($tanggalAkhir)->endOfDay()
            ]);

        if (!empty($supplier)) {
            $data->where('supplier.id', $supplier);
        }

        if (!empty($produk)) {
            $data->where('produk.kd_produk', $produk);
        }

        $results = $data->groupBy('produk.kd_produk', 'produk.judul', 'supplier.nama_supplier', 'produk.harga_modal', 'transaksi.created_at')
            ->get();

        // Implement your Excel export logic here using $results
        // Return Excel file (implementasi sesuai library Excel yang digunakan)
    }
}
