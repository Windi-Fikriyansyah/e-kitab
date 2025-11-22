<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;

class Transaksi extends Controller
{
    // Tampilkan daftar produk
    public function index(Request $request)
    {
        $search = $request->query('search');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();
        return view('transaksi.index', compact('products'));
    }

    public function cariData(Request $request)
    {
        $query = DB::table('transaksi');

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%")
                    ->orWhere('TotalAmount', 'like', "%{$search}%")
                    ->orWhere('PaymentAmount', 'like', "%{$search}%");
            });
        }

        // Pagination (Opsional - pilih salah satu)
        // Opsi 1: Dengan Pagination
        $transactions = $query->latest()->paginate(10);

        // Opsi 2: Tanpa Pagination (Collection biasa)
        // $transactions = $query->latest()->get();

        return view('transaksi.riwayat', compact('transactions'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $products = Product::where('name', 'like', '%' . $search . '%')->get();

        return response()->json($products);
    }


    public function saveTransaction(Request $request)
    {

        try {
            $data = $request->validate([
                'cart' => 'required|array',
                'cart.*.id' => 'required|integer',
                'cart.*.quantity' => 'required|integer',
                'cart.*.price' => 'required|numeric',
                'cart.*.harga_modal' => 'required|numeric',
                'cart.*.nama_produk' => 'required',
                'cart.*.name' => 'required|string',
                'total' => 'required|numeric',
                'payment' => 'required|numeric',
                'change' => 'required|numeric',
            ]);

            DB::beginTransaction();

            $today = Carbon::now('Asia/Jakarta')->format('Ymd'); // Format tanggal: YYYYMMDD
            $lastTransaction = DB::table('transaksi')
                ->whereDate('created_at', Carbon::now('Asia/Jakarta')->toDateString())
                ->orderBy('created_at', 'desc')
                ->first();

            // Menentukan nomor urut transaksi berdasarkan transaksi terakhir hari ini
            $transactionNumber = $lastTransaction ? (int) substr($lastTransaction->transaction_code, -4) + 1 : 1;
            $transactionCode = 'TRX-' . $today . '-' . str_pad($transactionNumber, 4, '0', STR_PAD_LEFT); // Format kode transaksi

            // Membuat catatan transaksi
            $transactionId = DB::table('transaksi')->insertGetId([
                'transaction_code' => $transactionCode,
                'TotalAmount' => $data['total'],
                'PaymentAmount' => $data['payment'],
                'user_id' => Auth::id(),
                'created_at' => Carbon::now('Asia/Jakarta'),
            ]);

            // Membuat detail transaksi dan memperbarui stok produk
            foreach ($data['cart'] as $item) {
                // Memperbarui stok produk
                DB::table('products')
                    ->where('id', $item['id'])
                    ->decrement('stock', $item['quantity']);

                // Menyisipkan detail transaksi
                DB::table('transaksidetails')->insert([
                    'TransactionId' => $transactionId,
                    'ProductId' => $item['id'],
                    'Quantity' => $item['quantity'],
                    'selling_price' => $item['price'],
                    'purchase_price' => $item['harga_modal'],
                    'nama_produk' => $item['nama_produk'],
                    'created_at' => Carbon::now('Asia/Jakarta'),
                ]);
            }

            DB::commit();

            // Mengembalikan struk transaksi
            return response()->json([
                'success' => true,
                'receipt' => [
                    'date' => Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s'),
                    'total' => $data['total'],
                    'payment' => $data['payment'],
                    'change' => $data['change'],
                    'items' => $data['cart'],
                    'transaction_code' => $transactionCode,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Mencatat kesalahan untuk debugging
            \Log::error('Transaksi gagal: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductByBarcode($barcode)
    {

        $product = Product::where('barcode', $barcode)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function getProductById($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product);
    }

    public function showTransactionHistory()
    {
        // Fetch transactions from the database using the query builder
        $transactions = DB::table('transaksi')
            ->where('transaksi.user_id', auth()->id())
            ->select('transaksi.*')
            ->orderBy('transaksi.created_at', 'desc') // Mengurutkan berdasarkan tanggal terbaru
            ->get();


        // Pass data to the view
        return view('transaksi.riwayat', compact('transactions'));
    }
}
