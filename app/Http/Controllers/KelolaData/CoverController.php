<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class CoverController extends Controller
{
    public function index()
    {
        return view('kelola_data.cover.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('cover')
            ->select(['id', 'nama_arab', 'nama_indonesia']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_arab', 'like', "%{$search}%")
                    ->orWhere('nama_indonesia', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.cover.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.cover.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('kelola_data.cover.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama cover (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama cover (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            DB::table('cover')->insert([
                'nama_arab' => $request->nama_arab,
                'nama_indonesia' => $request->nama_indonesia,
            ]);

            return redirect()->route('kelola_data.cover.index')
                ->with([
                    'message' => 'cover berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan cover: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $cover = DB::table('cover')->find($decryptedId);

        return view('kelola_data.cover.create', compact('cover'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama cover (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama cover (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            $affected = DB::table('cover')
                ->where('id', $decryptedId)
                ->update([
                    'nama_arab' => $request->nama_arab,
                    'nama_indonesia' => $request->nama_indonesia
                ]);

            if ($affected === 0) {
                return redirect()->back()
                    ->withInput()
                    ->with([
                        'message' => 'Tidak ada perubahan data atau data tidak ditemukan',
                        'message_type' => 'warning',
                        'message_title' => 'Peringatan'
                    ]);
            }

            return redirect()->route('kelola_data.cover.index')
                ->with([
                    'message' => 'Data cover berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data cover: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $cover = DB::table('cover')->find($decryptedId);

            if ($cover) {
                DB::table('cover')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'cover berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cover.'
            ], 500);
        }
    }
}
