<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class UkuranController extends Controller
{
    public function index()
    {
        return view('kelola_data.ukuran.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ukuran')
            ->select(['id', 'ukuran']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('ukuran', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.ukuran.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.ukuran.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('kelola_data.ukuran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ukuran' => 'required|string|max:255',
        ], [
            'ukuran.required' => 'Nama ukuran (Arab) wajib diisi',
            'ukuran.string' => 'Nama (Arab) harus berupa teks',
            'ukuran.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            DB::table('ukuran')->insert([
                'ukuran' => $request->ukuran
            ]);

            return redirect()->route('kelola_data.ukuran.index')
                ->with([
                    'message' => 'ukuran berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan ukuran: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $ukuran = DB::table('ukuran')->find($decryptedId);

        return view('kelola_data.ukuran.create', compact('ukuran'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'ukuran' => 'required|string|max:255',
        ], [
            'ukuran.required' => 'Nama ukuran (Arab) wajib diisi',
            'ukuran.string' => 'Nama (Arab) harus berupa teks',
            'ukuran.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            $affected = DB::table('ukuran')
                ->where('id', $decryptedId)
                ->update([
                    'ukuran' => $request->ukuran
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

            return redirect()->route('kelola_data.ukuran.index')
                ->with([
                    'message' => 'Data ukuran berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data ukuran: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $ukuran = DB::table('ukuran')->find($decryptedId);

            if ($ukuran) {
                DB::table('ukuran')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'ukuran berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ukuran.'
            ], 500);
        }
    }
}
