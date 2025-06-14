<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index()
    {
        return view('transaksi.barang_keluar.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('barang_keluar')
            ->join('produk', 'barang_keluar.id_produk', '=', 'produk.id')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'barang_keluar.id',
                'barang_keluar.stok_keluar',
                'barang_keluar.notes',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                'supplier.nama_supplier',
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('transaksi.barang_keluar.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('transaksi.barang_keluar.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('produk.judul', 'like', "%{$search}%")
                            ->orWhere('produk.kd_produk', 'like', "%{$search}%")
                            ->orWhere('produk.penulis', 'like', "%{$search}%")
                            ->orWhere('produk.kategori', 'like', "%{$search}%")
                            ->orWhere('produk.penerbit', 'like', "%{$search}%")
                            ->orWhere('supplier.nama_supplier', 'like', "%{$search}%")
                            ->orWhere('barang_keluar.stok_keluar', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('transaksi.barang_keluar.create');
    }

    public function getproduk(Request $request)
    {
        $search = $request->q;
        $id = $request->id;

        $query = DB::table('produk')
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select(
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                'produk.supplier as supplier_id',
                'supplier.nama_supplier as supplier_nama',
                'produk.stok'
            );

        if (!empty($id)) {
            // Jika ada ID, cari produk spesifik (untuk edit)
            $query->where('produk.id', $id);
            $results = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->judul . ' | ' . $item->kategori . ' | ' . ($item->supplier_nama ?? ''),
                    'kd_produk' => $item->kd_produk,
                    'penulis' => $item->penulis,
                    'kategori' => $item->kategori,
                    'penerbit' => $item->penerbit,
                    'supplier_id' => $item->supplier_id,
                    'supplier_nama' => $item->supplier_nama,
                    'stok' => $item->stok
                ];
            });
        } else {
            // Jika pencarian biasa
            $query->when(!empty($search), function ($q) use ($search) {
                $q->where('produk.judul', 'LIKE', "%{$search}%")
                    ->orWhere('produk.kategori', 'LIKE', "%{$search}%")
                    ->orWhere('supplier.nama_supplier', 'LIKE', "%{$search}%");
            })->limit(10);

            $results = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->judul . ' | ' . $item->kategori . ' | ' . ($item->supplier_nama ?? ''),
                    'kd_produk' => $item->kd_produk,
                    'penulis' => $item->penulis,
                    'kategori' => $item->kategori,
                    'penerbit' => $item->penerbit,
                    'supplier_id' => $item->supplier_id,
                    'supplier_nama' => $item->supplier_nama,
                    'stok' => $item->stok
                ];
            });
        }

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required',
            'stok_keluar' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Cek stok tersedia
            $produk = DB::table('produk')->where('id', $request->id_produk)->first();
            if ($produk->stok < $request->stok_keluar) {
                return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $produk->stok);
            }

            // Tambahkan barang keluar
            DB::table('barang_keluar')->insert([
                'id_produk' => $request->id_produk,
                'stok_keluar' => $request->stok_keluar,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Kurangi stok produk
            DB::table('produk')
                ->where('id', $request->id_produk)
                ->decrement('stok', $request->stok_keluar);

            DB::commit();

            return redirect()->route('transaksi.barang_keluar.index')
                ->with('message', 'Barang keluar berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan barang keluar: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_keluar = DB::table('barang_keluar')
                ->where('id', $decryptedId)
                ->first();

            if (!$barang_keluar) {
                return redirect()->route('transaksi.barang_keluar.index')
                    ->with('error', 'Data barang keluar tidak ditemukan');
            }

            return view('transaksi.barang_keluar.create', compact('barang_keluar'));
        } catch (\Exception $e) {
            return redirect()->route('transaksi.barang_keluar.index')
                ->with('error', 'Gagal memuat data barang keluar');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_produk' => 'required',
            'stok_keluar' => 'required|numeric|min:1',
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_keluar = DB::table('barang_keluar')->find($decryptedId);

            if (!$barang_keluar) {
                return back()->with('error', 'Data barang keluar tidak ditemukan');
            }

            DB::beginTransaction();

            // Cek stok tersedia (untuk kasus perubahan produk atau peningkatan jumlah keluar)
            $produk = DB::table('produk')->where('id', $request->id_produk)->first();

            // Hitung selisih stok
            $stokDifference = $request->stok_keluar - $barang_keluar->stok_keluar;

            // Jika ada permintaan untuk menambah jumlah keluar, cek stok
            if ($stokDifference > 0) {
                $stokTersedia = $produk->stok;
                if ($stokTersedia < $stokDifference) {
                    return back()->with('error', 'Stok tidak mencukupi untuk penambahan ini. Stok tersedia: ' . $stokTersedia);
                }
            }

            // Update barang keluar
            DB::table('barang_keluar')
                ->where('id', $decryptedId)
                ->update([
                    'id_produk' => $request->id_produk,
                    'stok_keluar' => $request->stok_keluar,
                    'notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            // Update stok produk
            if ($stokDifference != 0) {
                DB::table('produk')
                    ->where('id', $request->id_produk)
                    ->decrement('stok', $stokDifference);
            }

            DB::commit();

            return redirect()->route('transaksi.barang_keluar.index')
                ->with('message', 'Barang keluar berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui barang keluar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_keluar = DB::table('barang_keluar')->find($decryptedId);

            if ($barang_keluar) {
                DB::beginTransaction();

                // Kembalikan stok produk
                DB::table('produk')
                    ->where('id', $barang_keluar->id_produk)
                    ->increment('stok', $barang_keluar->stok_keluar);

                // Hapus barang keluar
                DB::table('barang_keluar')->where('id', $decryptedId)->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Barang keluar berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang keluar.'
            ], 500);
        }
    }
}
