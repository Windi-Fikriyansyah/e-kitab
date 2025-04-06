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
        return view('laporan.penjualan.index');
    }

    public function exportSalesReport(Request $request)
{

    $start_date = $request->get('start_date');
    $end_date = $request->get('end_date');
    $product_id = $request->get('product_id');

    // Export with the provided filters
    return Excel::download(new SalesReportExport($start_date, $end_date, $product_id), 'laporan_penjualan.xlsx');
}

    public function load(Request $request)
    {
        try {
            $query = DB::table('transaksidetails')
                ->join('transaksi', 'transaksidetails.TransactionId', '=', 'transaksi.Id')
                ->join('users', 'transaksi.user_id', '=', 'users.id')
                ->select(
                    'transaksidetails.created_at',
                    'users.name as nama_kasir',
                    'transaksidetails.selling_price',
                    'transaksidetails.nama_produk',
                    'transaksidetails.Quantity',
                    DB::raw('transaksidetails.selling_price * transaksidetails.Quantity as total')
                );

            if ($request->filled(['start_date', 'end_date'])) {
                $query->whereBetween('transaksidetails.created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }



            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y H:i');
                })
                ->editColumn('selling_price', function($row) {
                    return number_format($row->selling_price, 0, ',', '.');
                })
                ->editColumn('total', function($row) {
                    return number_format($row->total, 0, ',', '.');
                })
                ->rawColumns(['created_at', 'selling_price', 'total'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProducts()
    {
        try {
            $products = DB::table('products')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
