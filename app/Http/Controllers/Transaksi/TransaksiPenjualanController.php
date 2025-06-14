<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class TransaksiPenjualanController extends Controller
{
    public function index()
    {
        return view('transaksi.transaksi_penjualan.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                'supplier.nama_supplier as supplier',
                'produk.stok',
                'produk.harga_jual'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '';
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('produk.kd_produk', 'like', "%{$search}%")
                            ->orWhere('produk.judul', 'like', "%{$search}%")
                            ->orWhere('produk.penulis', 'like', "%{$search}%")
                            ->orWhere('produk.kategori', 'like', "%{$search}%")
                            ->orWhere('produk.penerbit', 'like', "%{$search}%")
                            ->orWhere('supplier.nama_supplier', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function simpanTransaksi(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi input
            $request->validate([
                'customer' => 'required|exists:customer,id',
                'payment_method' => 'required|in:tunai,transfer,dp',
                'payment_status' => 'required|in:lunas,hutang',
                'items' => 'required|array|min:1',
                'items.*.kd_produk' => 'required|exists:produk,kd_produk',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'diskon_persen' => 'numeric|min:0',
                'total' => 'required|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
            ]);



            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Simpan data transaksi
            $transaksiId = DB::table('transaksi')->insertGetId([
                'kode_transaksi' => $kodeTransaksi,
                'id_customer' => $request->customer,
                'nama_customer' => DB::table('customer')->where('id', $request->customer)->value('nama'),
                'no_hp_customer' => DB::table('customer')->where('id', $request->customer)->value('no_hp'),
                'alamat_customer' => DB::table('customer')->where('id', $request->customer)->value('alamat'),
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'subtotal' => $request->subtotal,
                'discount' => $request->diskon_persen ?? 0,
                'total' => $request->total,
                'paid_amount' => $request->paid_amount,
                'remaining_amount' => max(0, $request->total - $request->paid_amount),
                'change_amount' => max(0, $request->paid_amount - $request->total),
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan item transaksi
            foreach ($request->items as $item) {
                DB::table('transaksi_items')->insert([
                    'id_transaksi' => $transaksiId,
                    'kd_produk' => $item['kd_produk'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update stok produk
                DB::table('produk')
                    ->where('kd_produk', $item['kd_produk'])
                    ->decrement('stok', $item['quantity']);
            }

            // Update deposit customer jika ada
            if ($request->used_deposit > 0) {
                DB::table('customer')
                    ->where('id', $request->customer)
                    ->decrement('deposit', $request->used_deposit);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'kode_transaksi' => $kodeTransaksi,
                    'transaksi_id' => $transaksiId
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getcustomer(Request $request)
    {
        $search = $request->q;

        $customer = DB::table('customer')
            ->select('id', 'nama', 'no_hp', 'deposit')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('no_hp', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $customer->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama . ' | ' . $item->no_hp,
                'deposit' => $item->deposit
            ];
        });

        return response()->json($data);
    }

    public function simpanCustomer(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20|unique:customer,no_hp',
            'alamat' => 'required|string'
        ], [
            // Pesan validasi untuk field nama
            'nama.required' => 'Nama customer harus diisi.',
            'nama.string' => 'Nama customer harus berupa teks.',
            'nama.max' => 'Nama customer maksimal 255 karakter.',

            // Pesan validasi untuk field no_hp
            'no_hp.required' => 'Nomor HP harus diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',

            // Pesan validasi untuk field alamat
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa teks.'
        ]);

        try {
            $customerId = DB::table('customer')->insertGetId([
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil disimpan',
                'data' => [
                    'id' => $customerId,
                    'nama' => $request->nama,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan customer: ' . $e->getMessage()
            ], 500);
        }
    }
}
