<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class SubKategoriController extends Controller
{
    public function index()
    {
        return view('kelola_data.sub_kategori.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('sub_kategori')
            ->join('kategori', 'sub_kategori.id_kategori', '=', 'kategori.id')
            ->select([
                'sub_kategori.id',
                'sub_kategori.nama_arab',
                'sub_kategori.nama_indonesia',
                'kategori.nama_arab as nama_kategori'
            ]);

        // Get the search value from the request
        $searchValue = $request->input('search.value') ?? $request->input('search_value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('sub_kategori.nama_arab', 'like', '%' . $searchValue . '%')
                    ->orWhere('kategori.nama_arab', 'like', '%' . $searchValue . '%')
                    ->orWhere('sub_kategori.nama_indonesia', 'like', '%' . $searchValue . '%');
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.sub_kategori.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.sub_kategori.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getkategori(Request $request)
    {
        $search = $request->q;

        $kategoris = DB::table('kategori')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $kategoris->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function create()
    {
        return view('kelola_data.sub_kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required',
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama sub_kategori (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama sub_kategori (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            DB::table('sub_kategori')->insert([
                'id_kategori' => $request->id_kategori,
                'nama_arab' => $request->nama_arab,
                'nama_indonesia' => $request->nama_indonesia,
            ]);

            return redirect()->route('kelola_data.sub_kategori.index')
                ->with([
                    'message' => 'Sub Kategori berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan Sub Kategori: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $sub_kategori = DB::table('sub_kategori')->find($decryptedId);

        return view('kelola_data.sub_kategori.create', compact('sub_kategori'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'id_kategori' => 'required',
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama sub_kategori (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama sub_kategori (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            $affected = DB::table('sub_kategori')
                ->where('id', $decryptedId)
                ->update([
                    'id_kategori' => $request->id_kategori,
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

            return redirect()->route('kelola_data.sub_kategori.index')
                ->with([
                    'message' => 'Data Sub Kategori berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data sub_kategori: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $sub_kategori = DB::table('sub_kategori')->find($decryptedId);

            if ($sub_kategori) {
                DB::table('sub_kategori')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Sub Kategori berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Sub Kategori.'
            ], 500);
        }
    }
}
