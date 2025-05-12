<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class Dashboard extends Controller
{
    public function index(Request $request)
    {
        // Mengambil tipe user yang sedang login
        $user = Auth::user();

        // Mengecek tipe user dan mengarahkan sesuai
        if ($user->tipe === 'owner') {
            return redirect()->route('dashboard-owner'); // Arahkan ke halaman dashboard untuk tipe owner
        } elseif ($user->tipe === 'kasir') {
            return redirect()->route('home'); // Arahkan ke halaman home untuk tipe kasir
        }

        // Jika tidak sesuai tipe, bisa redirect ke halaman default atau login
        return redirect()->route('login');
    }


    public function dashboard(Request $request)
    {
        // Initialize variables
        $omset = 0;
        $pengeluaran = 0;
        $profit = 0;
        $jumlahTransaksi = 0;
        $bestSellingProducts = [];
        $lowStockProducts = [];

        // Determine the period based on request
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query for best selling products with date filtering applied
        $bestSellingQuery = DB::table('transaksidetails')
            ->join('products', 'transaksidetails.ProductId', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(transaksidetails.quantity) as total_quantity'),
                DB::raw('SUM(transaksidetails.selling_price * transaksidetails.quantity) as total_sales')
            );

        // Apply date filters to the best selling query
        if ($startDate && $endDate) {
            $bestSellingQuery->whereBetween('transaksidetails.created_at', [$startDate, $endDate]);
        } elseif ($period == 'today') {
            $bestSellingQuery->whereDate('transaksidetails.created_at', now());
        } else {
            // This month's data (default)
            $bestSellingQuery->whereMonth('transaksidetails.created_at', now()->month)
                ->whereYear('transaksidetails.created_at', now()->year);
        }

        // Finalize best selling query
        $bestSellingQuery->groupBy('transaksidetails.ProductId', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5);

        // Low stock query remains unchanged
        $lowStockQuery = DB::table('products')
            ->select('name', 'stock', DB::raw('20 as minimum_stock'))  // Using default 20 as minimum_stock
            ->where('stock', '<=', 20)  // Stock less than or equal to minimum
            ->orderBy('stock')
            ->limit(5);

        // Query for different periods
        if ($startDate && $endDate) {
            // Custom date range
            $omset = DB::table('transaksidetails')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum(DB::raw('selling_price * quantity'));

            $pengeluaran = DB::table('transaksidetails')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum(DB::raw('purchase_price * quantity'));

            $jumlahTransaksi = DB::table('transaksi')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        } elseif ($period == 'today') {
            // Today's data
            $omset = DB::table('transaksidetails')
                ->whereDate('created_at', now())
                ->sum(DB::raw('selling_price * quantity'));

            $pengeluaran = DB::table('transaksidetails')
                ->whereDate('created_at', now())
                ->sum(DB::raw('purchase_price * quantity'));

            $jumlahTransaksi = DB::table('transaksi')
                ->whereDate('created_at', now())
                ->count();
        } else {
            // This month's data (default)
            $omset = DB::table('transaksidetails')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum(DB::raw('selling_price * quantity'));

            $pengeluaran = DB::table('transaksidetails')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum(DB::raw('purchase_price * quantity'));

            $jumlahTransaksi = DB::table('transaksi')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        $profit = $omset - $pengeluaran;
        $lowStockProducts = $lowStockQuery->get();
        $bestSelling = $bestSellingQuery->get();


        return view('dashboard', compact(
            'omset',
            'pengeluaran',
            'profit',
            'jumlahTransaksi',
            'bestSelling',
            'lowStockProducts'
        ));
    }
}
