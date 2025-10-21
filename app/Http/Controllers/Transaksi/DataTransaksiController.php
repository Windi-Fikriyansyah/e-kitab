<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class DataTransaksiController extends Controller
{
    public function index()
    {
        return view('transaksi.data_transaksi.index');
    }

    public function detail($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $transaksi = DB::table('transaksi')->where('id', $decryptedId)->first();
            $items = DB::table('transaksi_items')
                ->join('produk', 'produk.kd_produk', '=', 'transaksi_items.kd_produk')
                ->where('id_transaksi', $decryptedId)
                ->select([
                    'transaksi_items.*',
                    'produk.judul'
                ])
                ->get();

            return response()->json([
                'success' => true,
                'transaksi' => $transaksi,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data transaksi tidak ditemukan'
            ]);
        }
    }

    public function load(Request $request)
    {
        $query = DB::table('transaksi')
            ->select([
                'id',
                'kode_transaksi',
                'nama_customer',
                'no_hp_customer',
                'payment_status',
                'total',
                'paid_amount',
                'remaining_amount',
                'created_at'
            ])
            ->orderByRaw("CASE WHEN payment_status = 'hutang' THEN 0 ELSE 1 END") // Hutang di atas
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $detailButton = '<button class="btn btn-sm btn-primary right-gap detail-btn" data-url="' . route('transaksi.data_transaksi.detail', Crypt::encrypt($row->id)) . '"><i class="fas fa-eye"></i></button>';
                $printButton = '<button class="btn btn-sm btn-info right-gap print-btn" data-id="' . $row->id . '"><i class="fas fa-print"></i></button>';
                $payButton = '';
                if ($row->payment_status == 'hutang') {
                    $payButton = '<button class="btn btn-sm btn-success right-gap pay-btn" data-url="' . route('transaksi.data_transaksi.pay', Crypt::encrypt($row->id)) . '" data-remaining="' . $row->remaining_amount . '"><i class="fas fa-money-bill-wave"></i> Bayar</button>';
                }

                $deleteButton = '';
                if ($row->payment_status != 'lunas') {
                    $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('transaksi.data_transaksi.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                }
                return $detailButton . $printButton . $payButton . $deleteButton;
            })
            ->editColumn('total', function ($row) {
                if ($row->payment_status === 'hutang') {
                    return 'Rp ' . number_format($row->paid_amount, 0, ',', '.') .
                        ' / Rp ' . number_format($row->remaining_amount, 0, ',', '.');
                }
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('kode_transaksi', 'like', "%{$search}%")
                            ->orWhere('nama_customer', 'like', "%{$search}%")
                            ->orWhere('no_hp_customer', 'like', "%{$search}%")
                            ->orWhere('payment_status', 'like', "%{$search}%")
                            ->orWhere('total', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function cetakInvoice($id)
    {

        $transaksi = DB::table('transaksi')
            ->join('ekspedisi', 'transaksi.ekspedisi', '=', 'ekspedisi.nama_ekspedisi')
            ->where('transaksi.id', $id)
            ->select('transaksi.*', 'ekspedisi.nama_ekspedisi', 'ekspedisi.ekspedisi_logo')
            ->first();

        if (!$transaksi) {
            abort(404);
        }

        $items = DB::table('transaksi_items')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('id_transaksi', $id)
            ->select('transaksi_items.*', 'produk.judul', 'produk.penulis')
            ->get();
        $profilPerusahaan = DB::table('profile_perusahaan')->first();

        return view('transaksi.transaksi_penjualan.invoice', compact('transaksi', 'items', 'profilPerusahaan'));
    }
    public function cetakSuratJalan($id)
    {
        // Mengambil data transaksi dengan join ekspedisi
        $transaksi = DB::table('transaksi')
            ->leftJoin('ekspedisi', 'transaksi.ekspedisi', '=', 'ekspedisi.nama_ekspedisi')
            ->where('transaksi.id', $id)
            ->select(
                'transaksi.*',
                'ekspedisi.nama_ekspedisi',
                'ekspedisi.ekspedisi_logo'
            )
            ->first();

        if (!$transaksi) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        // Mengambil items transaksi dengan detail produk
        $items = DB::table('transaksi_items')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('id_transaksi', $id)
            ->select(
                'transaksi_items.*',
                'produk.judul',
                'produk.penulis',
                'produk.penerbit',
                // Hitung total price jika belum ada
                DB::raw('(transaksi_items.quantity * transaksi_items.unit_price) as calculated_total')
            )
            ->get()
            ->map(function ($item) {
                // Pastikan total_price ada nilai
                if (!$item->total_price) {
                    $item->total_price = $item->calculated_total;
                }
                return $item;
            });

        // Mengambil profil perusahaan
        $profilPerusahaan = DB::table('profile_perusahaan')->first();

        // Hitung subtotal jika tidak ada di database
        if (!$transaksi->subtotal) {
            $transaksi->subtotal = $items->sum('total_price');
        }

        return view('transaksi.data_transaksi.surat_jalan', compact('transaksi', 'items', 'profilPerusahaan'));
    }
    public function pay(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $transaksi = DB::table('transaksi')->where('id', $decryptedId)->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ]);
            }

            if ($transaksi->payment_status != 'hutang') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ini tidak memiliki hutang'
                ]);
            }

            $paymentAmount = $request->input('payment_amount');
            if (!is_numeric($paymentAmount)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran harus angka'
                ]);
            }

            $paymentAmount = (float) $paymentAmount;
            if ($paymentAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran harus lebih dari 0'
                ]);
            }

            $remainingAmount = $transaksi->remaining_amount - $paymentAmount;
            if ($remainingAmount < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran melebihi sisa hutang'
                ]);
            }

            $newPaidAmount = $transaksi->paid_amount + $paymentAmount;
            $newStatus = $remainingAmount <= 0 ? 'lunas' : 'hutang';

            DB::table('transaksi')
                ->where('id', $decryptedId)
                ->update([
                    'payment_status' => $newStatus,
                    'paid_amount' => $newPaidAmount,
                    'remaining_amount' => max($remainingAmount, 0),
                    'updated_at' => now()
                ]);

            // Save payment history
            DB::table('payment_history')->insert([
                'transaction_id' => $decryptedId,
                'payment_amount' => $paymentAmount,
                'payment_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat',
                'new_status' => $newStatus,
                'remaining_amount' => $remainingAmount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function paymentHistory($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $history = DB::table('payment_history')
                ->where('transaction_id', $decryptedId)
                ->orderBy('payment_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat pembayaran'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            // Get transaction items first
            $items = DB::table('transaksi_items')
                ->where('id_transaksi', $decryptedId)
                ->get();

            // Start transaction
            DB::beginTransaction();

            try {
                // Restore stock for each item
                foreach ($items as $item) {
                    DB::table('produk')
                        ->where('kd_produk', $item->kd_produk)
                        ->increment('stok', $item->quantity);
                }

                // Delete transaction items
                DB::table('transaksi_items')
                    ->where('id_transaksi', $decryptedId)
                    ->delete();

                // Delete payment history
                DB::table('payment_history')
                    ->where('transaction_id', $decryptedId)
                    ->delete();

                // Delete the transaction
                DB::table('transaksi')
                    ->where('id', $decryptedId)
                    ->delete();

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil dihapus dan stok produk dikembalikan'
                ]);
            } catch (\Exception $e) {
                // Rollback transaction if error occurs
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendekripsi ID transaksi'
            ]);
        }
    }
}
