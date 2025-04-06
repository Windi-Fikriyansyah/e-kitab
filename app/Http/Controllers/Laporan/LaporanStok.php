<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class LaporanStok extends Controller
{
    public function index()
    {


        return view('laporan.stok.index');
    }

    public function load(Request $request)
{
    // Ambil data produk dari database untuk DataTables
    $query = DB::table('products')
    ->select(['id', 'barcode', 'name', 'category', 'purchase_price', 'selling_price', 'stock', 'satuan', 'photo'])
    ->orderBy('stock', 'asc')
    ->get();



    // Cek jika ada pencarian yang dikirim dari DataTables
    return DataTables::of($query)
        ->rawColumns([]) // Render kolom 'aksi' sebagai HTML
        ->make(true);

}

}
