<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        return view('transaksi.barang_masuk.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('barang_masuk')
            ->join('produk', 'barang_masuk.id_produk', '=', 'produk.id')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'barang_masuk.id',
                'barang_masuk.stok_masuk',
                'barang_masuk.notes',
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
                $editButton = '<a href="' . route('transaksi.barang_masuk.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('transaksi.barang_masuk.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
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
                            ->orWhere('barang_masuk.stok_masuk', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('transaksi.barang_masuk.create');
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
                    ->orWhere('produk.kd_produk', 'LIKE', "%{$search}%")
                    ->orWhere('produk.kategori', 'LIKE', "%{$search}%")
                    ->orWhere('supplier.nama_supplier', 'LIKE', "%{$search}%");
            })->limit(10);

            $results = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->judul . ' | ' . $item->kd_produk . '' . ' | ' . $item->kategori . ' | ' . ($item->supplier_nama ?? ''),
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
            'stok_masuk' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Tambahkan stok masuk
            DB::table('barang_masuk')->insert([
                'id_produk' => $request->id_produk,
                'stok_masuk' => $request->stok_masuk,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            DB::table('produk')
                ->where('id', $request->id_produk)
                ->increment('stok', $request->stok_masuk);

            DB::commit();

            return redirect()->route('transaksi.barang_masuk.index')
                ->with('message', 'Barang masuk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan barang masuk: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_masuk = DB::table('barang_masuk')
                ->where('id', $decryptedId)
                ->first();

            if (!$barang_masuk) {
                return redirect()->route('transaksi.barang_masuk.index')
                    ->with('error', 'Data barang masuk tidak ditemukan');
            }

            return view('transaksi.barang_masuk.create', compact('barang_masuk'));
        } catch (\Exception $e) {
            return redirect()->route('transaksi.barang_masuk.index')
                ->with('error', 'Gagal memuat data barang masuk');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_produk' => 'required',
            'stok_masuk' => 'required|numeric|min:1',
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_masuk = DB::table('barang_masuk')->find($decryptedId);

            if (!$barang_masuk) {
                return back()->with('error', 'Data barang masuk tidak ditemukan');
            }

            DB::beginTransaction();

            // Hitung selisih stok
            $stokDifference = $request->stok_masuk - $barang_masuk->stok_masuk;

            // Update barang masuk
            DB::table('barang_masuk')
                ->where('id', $decryptedId)
                ->update([
                    'id_produk' => $request->id_produk,
                    'stok_masuk' => $request->stok_masuk,
                    'notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            // Update stok produk jika ada perubahan
            if ($stokDifference != 0) {
                DB::table('produk')
                    ->where('id', $request->id_produk)
                    ->increment('stok', $stokDifference);
            }

            DB::commit();

            return redirect()->route('transaksi.barang_masuk.index')
                ->with('message', 'Barang masuk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui barang masuk: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $barang_masuk = DB::table('barang_masuk')->find($decryptedId);

            if ($barang_masuk) {
                DB::beginTransaction();

                // Kurangi stok produk
                DB::table('produk')
                    ->where('id', $barang_masuk->id_produk)
                    ->decrement('stok', $barang_masuk->stok_masuk);

                // Hapus barang masuk
                DB::table('barang_masuk')->where('id', $decryptedId)->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Barang masuk berhasil dihapus.'
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
                'message' => 'Gagal menghapus barang masuk.'
            ], 500);
        }
    }
}
