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
        $profitToday = DB::table('transaksidetails')
            ->whereDate('created_at', now())
            ->sum(DB::raw('(selling_price - purchase_price) * quantity'));

        // Hitung omset hari ini
        $omsetToday = DB::table('transaksidetails')
            ->whereDate('created_at', now())
            ->sum(DB::raw('selling_price * quantity'));

        // Hitung profit bulan ini
        $profitThisMonth = DB::table('transaksidetails')
            ->whereMonth('created_at', now()->month)
            ->sum(DB::raw('(selling_price - purchase_price) * quantity'));

        // Hitung omset bulan ini
        $omsetThisMonth = DB::table('transaksidetails')
            ->whereMonth('created_at', now()->month)
            ->sum(DB::raw('selling_price * quantity'));

        $transactionsToday = DB::table('transaksi')
            ->whereDate('created_at', now())
            ->count();

        $transactionsMonth = DB::table('transaksi')
            ->whereMonth('created_at', now()->month)
            ->count();

        $topProduct = DB::table('transaksidetails')
            ->select('nama_produk', DB::raw('SUM(Quantity) as total_quantity'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('nama_produk')
            ->orderByDesc('total_quantity')
            ->first();

            return view('dashboard', compact('profitToday', 'omsetToday', 'profitThisMonth', 'omsetThisMonth','transactionsToday','transactionsMonth','topProduct'));
    }

}
