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
        $totalIncome = DB::table('transaksi')
            ->where('payment_status', 'lunas')
            ->sum('total');
        // Total transaksi
        $totalTransactions = DB::table('transaksi')->count();

        // Total customer
        $totalCustomers = DB::table('customer')->count();

        $totalpiutang = DB::table('transaksi')
            ->where('payment_status', 'hutang')
            ->sum('total');

        // 5 Produk terlaris
        $bestSellers = DB::table('produk')
            ->join('transaksi_items', 'produk.kd_produk', '=', 'transaksi_items.kd_produk')
            ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
            ->where('transaksi.payment_status', 'lunas')
            ->select('produk.*', DB::raw('SUM(transaksi_items.quantity) as total_sold'))
            ->groupBy(
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.harga_jual',
                'produk.kategori',
                'produk.stok',
                'produk.created_at',
                'produk.updated_at'
            )
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $lowStockItems = DB::table('produk')
            ->where('stok', '<', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalIncome' => $totalIncome,
            'totalTransactions' => $totalTransactions,
            'totalCustomers' => $totalCustomers,
            'totalpiutang' => $totalpiutang,
            'bestSellers' => $bestSellers,
            'lowStockItems' => $lowStockItems
        ]);
    }
}
