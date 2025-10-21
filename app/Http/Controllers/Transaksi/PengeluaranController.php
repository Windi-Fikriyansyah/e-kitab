<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        return view('transaksi.pengeluaran.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('pengeluaran')
            ->select(['id', 'tanggal', 'kategori', 'deskripsi', 'nominal', 'metode_bayar', 'lampiran'])
            ->orderBy('tanggal', 'desc');

        return DataTables::of($query)
            ->addColumn('aksi', function ($row) {
                $editUrl = route('transaksi.pengeluaran.edit', Crypt::encrypt($row->id));
                $deleteUrl = route('transaksi.pengeluaran.destroy', Crypt::encrypt($row->id));
                return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                data-url="' . $deleteUrl . '" data-id="' . $row->id . '">
            <i class="fas fa-trash-alt"></i>
        </button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('transaksi.pengeluaran.create');
    }

    public function edit($id)
    {

        try {
            $decryptedId = Crypt::decrypt($id);

            $pengeluaran = DB::table('pengeluaran')->where('id', $decryptedId)->first();

            if (!$pengeluaran) {
                return redirect()->route('transaksi.pengeluaran.index')->with('message', 'Data pengeluaran tidak ditemukan.');
            }

            return view('transaksi.pengeluaran.create', compact('pengeluaran'));
        } catch (\Exception $e) {
            return redirect()->route('transaksi.pengeluaran.index')->with('message', 'ID pengeluaran tidak valid.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string',
            'deskripsi' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'metode_bayar' => 'required|string',
            'lampiran' => 'nullable|image|max:2048',
        ]);

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('pengeluaran', 'public')
            : null;

        DB::table('pengeluaran')->insert([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'metode_bayar' => $request->metode_bayar,
            'lampiran' => $lampiranPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('transaksi.pengeluaran.index')->with('message', 'Data pengeluaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);

        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string',
            'deskripsi' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'metode_bayar' => 'required|string',
            'lampiran' => 'nullable|image|max:2048',
        ]);

        $data = DB::table('pengeluaran')->where('id', $decryptedId)->first();
        $lampiranPath = $data->lampiran ?? null;

        if ($request->hasFile('lampiran')) {
            if ($lampiranPath && file_exists(storage_path('app/public/' . $lampiranPath))) {
                unlink(storage_path('app/public/' . $lampiranPath));
            }
            $lampiranPath = $request->file('lampiran')->store('pengeluaran', 'public');
        }

        DB::table('pengeluaran')->where('id', $decryptedId)->update([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'nominal' => $request->nominal,
            'metode_bayar' => $request->metode_bayar,
            'lampiran' => $lampiranPath,
            'updated_at' => now(),
        ]);

        return redirect()->route('transaksi.pengeluaran.index')->with('message', 'Data pengeluaran berhasil diperbarui!');
    }


    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $data = DB::table('pengeluaran')->where('id', $decryptedId)->first();
            if ($data && $data->lampiran && file_exists(storage_path('app/public/' . $data->lampiran))) {
                unlink(storage_path('app/public/' . $data->lampiran));
            }
            DB::table('pengeluaran')->where('id', $decryptedId)->delete();
            return response()->json(['success' => true, 'message' => 'Data pengeluaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data']);
        }
    }
}
