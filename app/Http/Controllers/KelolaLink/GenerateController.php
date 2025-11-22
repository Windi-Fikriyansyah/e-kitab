<?php

namespace App\Http\Controllers\KelolaLink;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function index()
    {
        return view('kelola_link.generate_deskripsi.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->leftJoin('generate_deskripsi', 'produk.id', '=', 'generate_deskripsi.id_produk')
            ->select([
                'produk.id',
                'produk.judul',
                'supplier.nama_supplier as supplier',
                'generate_deskripsi.id as generate_id'
            ]);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('produk.judul', 'like', "%{$search}%")
                    ->orWhere('supplier.nama_supplier', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                if ($row->generate_id) {
                    return '<a href="' . route('kelola_link.generate_deskripsi.edit', Crypt::encrypt($row->generate_id)) . '" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                }

                // Jika belum ada deskripsi, tampilkan tombol tambah
                return '<a href="' . route('kelola_link.generate_deskripsi.create', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-success"><i class="fas fa-plus"></i></a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $generate_deskripsi = DB::table('generate_deskripsi')->where('id_produk', $decryptedId)->first();
        $produk = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select('produk.*', 'supplier.nama_supplier')
            ->where('produk.id', $decryptedId)
            ->first();

        if (!$produk) {
            return redirect()->route('kelola_link.generate_deskripsi.index')
                ->with('message', 'Produk tidak ditemukan')
                ->with('message_type', 'danger');
        }

        return view('kelola_link.generate_deskripsi.create', compact('generate_deskripsi', 'produk'));
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $generate_deskripsi = DB::table('generate_deskripsi')
            ->where('id', $decryptedId)
            ->first();

        if (!$generate_deskripsi) {
            return redirect()->route('kelola_link.generate_deskripsi.index')
                ->with('message', 'Deskripsi tidak ditemukan')
                ->with('message_type', 'danger');
        }

        $produk = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select('produk.*', 'supplier.nama_supplier')
            ->where('produk.id', $generate_deskripsi->id_produk)
            ->first();

        return view('kelola_link.generate_deskripsi.create', compact('generate_deskripsi', 'produk'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);

        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'deskripsi_jadi_arab' => 'nullable|string',
            'deskripsi_jadi_indonesia' => 'nullable|string',
            'deskripsi_jadi_ig_arab' => 'nullable|string',
            'deskripsi_jadi_ig_indonesia' => 'nullable|string',
            'deskripsi_jadi_fb_arab' => 'nullable|string',
            'deskripsi_jadi_fb_indonesia' => 'nullable|string',
            'deskripsi_jadi_threads_arab' => 'nullable|string',
            'deskripsi_jadi_threads_indonesia' => 'nullable|string',
            'deskripsi_jadi_x_arab' => 'nullable|string',
            'deskripsi_jadi_x_indonesia' => 'nullable|string',
            'deskripsi_jadi_pinterest_arab' => 'nullable|string',
            'deskripsi_jadi_pinterest_indonesia' => 'nullable|string',
            'deskripsi_jadi_marketplace_arab' => 'nullable|string',
            'deskripsi_jadi_marketplace_indonesia' => 'nullable|string'
        ]);

        try {
            DB::table('generate_deskripsi')
                ->where('id', $decryptedId)
                ->update([
                    'deskripsi_wa_arab' => $request->deskripsi_jadi_arab,
                    'deskripsi_wa_indonesia' => $request->deskripsi_jadi_indonesia,
                    'deskripsi_ig_arab' => $request->deskripsi_jadi_ig_arab,
                    'deskripsi_ig_indonesia' => $request->deskripsi_jadi_ig_indonesia,
                    'deskripsi_fb_arab' => $request->deskripsi_jadi_fb_arab,
                    'deskripsi_fb_indonesia' => $request->deskripsi_jadi_fb_indonesia,
                    'deskripsi_threads_arab' => $request->deskripsi_jadi_threads_arab,
                    'deskripsi_threads_indonesia' => $request->deskripsi_jadi_threads_indonesia,
                    'deskripsi_x_arab' => $request->deskripsi_jadi_x_arab,
                    'deskripsi_x_indonesia' => $request->deskripsi_jadi_x_indonesia,
                    'deskripsi_pinterest_arab' => $request->deskripsi_jadi_pinterest_arab,
                    'deskripsi_pinterest_indonesia' => $request->deskripsi_jadi_pinterest_indonesia,
                    'deskripsi_marketplace_arab' => $request->deskripsi_jadi_marketplace_arab,
                    'deskripsi_marketplace_indonesia' => $request->deskripsi_jadi_marketplace_indonesia,
                    'updated_at' => now()
                ]);

            return redirect()->route('kelola_link.generate_deskripsi.index')
                ->with('message', 'Deskripsi berhasil diperbarui')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal memperbarui deskripsi: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'deskripsi_jadi_arab' => 'nullable|string',
            'deskripsi_jadi_indonesia' => 'nullable|string',
            'deskripsi_jadi_ig_arab' => 'nullable|string',
            'deskripsi_jadi_ig_indonesia' => 'nullable|string',
            'deskripsi_jadi_fb_arab' => 'nullable|string',
            'deskripsi_jadi_fb_indonesia' => 'nullable|string',
            'deskripsi_jadi_threads_arab' => 'nullable|string',
            'deskripsi_jadi_threads_indonesia' => 'nullable|string',
            'deskripsi_jadi_x_arab' => 'nullable|string',
            'deskripsi_jadi_x_indonesia' => 'nullable|string',
            'deskripsi_jadi_pinterest_arab' => 'nullable|string',
            'deskripsi_jadi_pinterest_indonesia' => 'nullable|string',
            'deskripsi_jadi_marketplace_arab' => 'nullable|string',
            'deskripsi_jadi_marketplace_indonesia' => 'nullable|string'
        ]);

        try {
            DB::table('generate_deskripsi')->insert([
                'id_produk' => $request->id_produk,
                'deskripsi_wa_arab' => $request->deskripsi_jadi_arab,
                'deskripsi_wa_indonesia' => $request->deskripsi_jadi_indonesia,
                'deskripsi_ig_arab' => $request->deskripsi_jadi_ig_arab,
                'deskripsi_ig_indonesia' => $request->deskripsi_jadi_ig_indonesia,
                'deskripsi_fb_arab' => $request->deskripsi_jadi_fb_arab,
                'deskripsi_fb_indonesia' => $request->deskripsi_jadi_fb_indonesia,
                'deskripsi_threads_arab' => $request->deskripsi_jadi_threads_arab,
                'deskripsi_threads_indonesia' => $request->deskripsi_jadi_threads_indonesia,
                'deskripsi_x_arab' => $request->deskripsi_jadi_x_arab,
                'deskripsi_x_indonesia' => $request->deskripsi_jadi_x_indonesia,
                'deskripsi_pinterest_arab' => $request->deskripsi_jadi_pinterest_arab,
                'deskripsi_pinterest_indonesia' => $request->deskripsi_jadi_pinterest_indonesia,
                'deskripsi_marketplace_arab' => $request->deskripsi_jadi_marketplace_arab,
                'deskripsi_marketplace_indonesia' => $request->deskripsi_jadi_marketplace_indonesia,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('kelola_link.generate_deskripsi.index')
                ->with('message', 'Deskripsi berhasil disimpan')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal menyimpan deskripsi: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    public function getformat(Request $request)
    {

        $search = $request->q;
        $language = $request->language;
        $platform = $request->platform;

        $query = DB::table('deskripsi')
            ->select('id', 'nama_format', 'format_deskripsi')
            ->where('language', $language)
            ->where('platform', $platform)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_format', 'LIKE', "%{$search}%");
            })
            ->limit(10);

        $data = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_format' => $item->nama_format,
                'format_deskripsi' => $item->format_deskripsi,
                'text' => $item->nama_format
            ];
        });

        return response()->json($data);
    }

    public function getProdukDetail(Request $request)
    {
        $produk = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select('produk.*', 'supplier.nama_supplier')
            ->where('produk.id', $request->id)
            ->first();

        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'judul' => $produk->judul,
            'cover' => $produk->cover,
            'kertas' => $produk->kertas,
            'kualitas' => $produk->kualitas,
            'harakat' => $produk->harakat,
            'halaman' => $produk->halaman,
            'berat' => $produk->berat,
            'kategori' => $produk->kategori,
            'penulis' => $produk->penulis,
            'penerbit' => $produk->penerbit,
            'stok' => $produk->stok,
            'ukuran' => $produk->ukuran,
            'nama_supplier' => $produk->nama_supplier
        ]);
    }

    public function getProdukDetailIndo(Request $request)
    {
        $produk = DB::table('produk_indo')
            ->join('produk', 'produk_indo.id_produk', '=', 'produk.id')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select('produk_indo.*', 'produk.halaman', 'produk.berat', 'produk.ukuran', 'produk.halaman', 'supplier.nama_supplier', 'produk.stok')
            ->where('produk_indo.id_produk', $request->id)
            ->first();

        if (!$produk) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'judul_indo' => $produk->judul_indo,
            'cover_indo' => $produk->cover_indo,
            'kertas_indo' => $produk->kertas_indo,
            'kualitas_indo' => $produk->kualitas_indo,
            'harakat_indo' => $produk->harakat_indo,
            'penulis_indo' => $produk->penulis_indo,
            'halaman' => $produk->halaman,
            'berat' => $produk->berat,
            'kategori_indo' => $produk->kategori_indo,
            'penerbit_indo' => $produk->penerbit_indo,
            'stok' => $produk->stok,
            'ukuran' => $produk->ukuran,
            'nama_supplier' => $produk->nama_supplier
        ]);
    }
}
