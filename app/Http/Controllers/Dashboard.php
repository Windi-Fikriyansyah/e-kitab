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

        return view('dashboard');
    }
}
