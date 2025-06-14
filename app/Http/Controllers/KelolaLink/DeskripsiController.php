<?php

namespace App\Http\Controllers\KelolaLink;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class DeskripsiController extends Controller
{
    public function index()
    {
        return view('kelola_link.deskripsi.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('deskripsi')
            ->select(['id', 'nama_format', 'format_deskripsi', 'platform']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_format', 'like', "%{$search}%")
                    ->orWhere('format_deskripsi', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_link.deskripsi.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_link.deskripsi.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $productColumns = ['judul', 'cover', 'kertas', 'kualitas', 'harakat', 'halaman', 'berat', 'ukuran', 'kategori', 'penulis', 'penerbit', 'stok'];
        return view('kelola_link.deskripsi.create', compact('productColumns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_format' => 'required|string|max:255',
            'platform' => 'required',
            'format_indonesia' => 'nullable|string',
            'format_arab' => 'nullable|string',
            'language' => 'required|in:1,2',
        ], [
            'nama_format.required' => 'Nama format wajib diisi',
        ]);

        // Validasi bahwa minimal salah satu format harus diisi
        if (empty($request->format_indonesia) && empty($request->format_arab)) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Minimal salah satu format (Indonesia atau Arab) harus diisi')
                ->with('message_type', 'danger');
        }

        try {
            // Pilih format_deskripsi berdasarkan language
            $formatDeskripsi = $request->language == 1
                ? $request->format_indonesia
                : $request->format_arab;

            DB::table('deskripsi')->insert([
                'nama_format' => $request->nama_format,
                'platform' => $request->platform,
                'format_deskripsi' => $formatDeskripsi,
                'language' => $request->language,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()->route('kelola_link.deskripsi.index')
                ->with('message', 'Deskripsi berhasil ditambahkan')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal menambahkan deskripsi: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }


    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $deskripsi = DB::table('deskripsi')->find($decryptedId);

        if (!$deskripsi) {
            return redirect()->route('kelola_link.deskripsi.index')
                ->with('message', 'Data tidak ditemukan')
                ->with('message_type', 'danger');
        }

        // Set format berdasarkan language yang ada di database
        if ($deskripsi->language == 1) {
            $deskripsi->format_indonesia = $deskripsi->format_deskripsi;
        } else {
            $deskripsi->format_arab = $deskripsi->format_deskripsi;
        }

        $productColumns = ['judul', 'cover', 'kertas', 'kualitas', 'harakat', 'halaman', 'berat', 'ukuran', 'kategori', 'penulis', 'penerbit', 'stok'];
        return view('kelola_link.deskripsi.create', compact('deskripsi', 'productColumns'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_format' => 'required|string|max:255',
            'platform' => 'required',
            'format_indonesia' => 'nullable|string',
            'format_arab' => 'nullable|string',
            'language' => 'required|in:1,2',
        ], [
            'nama_format.required' => 'Nama format wajib diisi',
        ]);

        // Validasi bahwa format harus diisi sesuai dengan language yang dipilih
        if (($request->language == 1 && empty($request->format_indonesia))) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Format Indonesia wajib diisi')
                ->with('message_type', 'danger');
        }

        if ($request->language == 2 && empty($request->format_arab)) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Format Arab wajib diisi')
                ->with('message_type', 'danger');
        }

        try {
            $decryptedId = Crypt::decrypt($id);

            DB::table('deskripsi')->where('id', $decryptedId)->update([
                'nama_format' => $request->nama_format,
                'platform' => $request->platform,
                'format_deskripsi' => $request->language == 1 ? $request->format_indonesia : $request->format_arab,
                'language' => $request->language,
                'updated_at' => now()
            ]);

            return redirect()->route('kelola_link.deskripsi.index')
                ->with('message', 'Deskripsi berhasil diperbarui')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'Gagal memperbarui deskripsi: ' . $e->getMessage())
                ->with('message_type', 'danger');
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $deskripsi = DB::table('deskripsi')->find($decryptedId);

            if ($deskripsi) {
                DB::table('deskripsi')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Deskripsi berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus deskripsi.'
            ], 500);
        }
    }
}
