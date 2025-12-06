<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualan extends Controller
{
    public function index()
    {
        $kasirs = DB::table('users')->where('role', '2')->get();
        $customers = DB::table('customer')->get();

        return view('laporan.penjualan.index', compact('kasirs', 'customers'));
    }

    public function export(Request $request)
    {
        $start_date = $request->get('tanggal_awal');
        $end_date = $request->get('tanggal_akhir');
        $kasir_id = $request->get('kasir');
        $customer_id = $request->get('customer');

        return Excel::download(
            new SalesReportExport($start_date, $end_date, $kasir_id, $customer_id),
            'laporan_penjualan_' . date('YmdHis') . '.xlsx'
        );
    }

    public function load(Request $request)
    {
        try {
            $query = DB::table('transaksi')
                ->leftJoin('users', 'transaksi.kasir', '=', 'users.id')
                ->select(
                    'transaksi.*',
                    'users.name as kasir_name'
                )
                ->where(function ($query) {
                    // Tambahkan kondisi where untuk payment_status
                    $query->where('transaksi.payment_status', 'lunas');
                });

            // Apply filters
            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('transaksi.created_at', [
                    Carbon::parse($request->tanggal_awal)->startOfDay(),
                    Carbon::parse($request->tanggal_akhir)->endOfDay()
                ]);
            }

            if ($request->filled('kasir')) {
                $query->where('transaksi.kasir', $request->kasir);
            }

            if ($request->filled('customer')) {
                $query->where('transaksi.nama_customer', $request->customer);
            }

            // Get totals for summary
            $totalQuery = clone $query;
            $totals = $totalQuery->select(
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(total) as total_nilai')
            )->first();

            $omsetQuery = clone $query;
            $omsetData = $omsetQuery
                ->select(DB::raw('SUM(transaksi.totalomset) as total_omset'))
                ->first();
            $labaQuery = clone $query;
            $labaData = $labaQuery
                ->select(DB::raw('SUM(transaksi.totallaba) as total_laba'))
                ->first();

            $modalQuery = clone $query;
            $modalData = $modalQuery
                ->leftJoin('transaksi_items', 'transaksi.id', '=', 'transaksi_items.id_transaksi')
                ->select(DB::raw('SUM(transaksi_items.quantity * transaksi_items.harga_modal) as total_modal'))
                ->first();





            return DataTables::of($query)
                ->with([
                    'total_transaksi' => $totals->total_transaksi ?? 0,
                    'total_nilai' => $totals->total_nilai ?? 0,
                    'total_omset'     => $omsetData->total_omset ?? 0,
                    'total_modal'     => $modalData->total_modal ?? 0,
                    'total_laba'      => $labaData->total_laba ?? 0
                ])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
