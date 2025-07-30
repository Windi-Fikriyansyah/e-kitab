<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return view('kelola_data.kategori.index');
    }

    public function get_api()
    {
        // Get all categories
        $kategoris = DB::table('kategori')->get();

        $result = [];
        foreach ($kategoris as $kategori) {
            // Get subcategories for each category
            $subkategoris = DB::table('sub_kategori')
                ->where('id_kategori', $kategori->id)
                ->get();

            // Build the category array with subcategories
            $kategoriData = [
                'id' => $kategori->id,
                'nama' => $kategori->nama_indonesia,
                'nama_arab' => $kategori->nama_arab,
                // Add other kategori fields as needed
                'subkategoris' => $subkategoris
            ];

            $result[] = $kategoriData;
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function get_api_show($id)
    {
        // Get the category
        $kategori = DB::table('kategori')->where('id', $id)->first();

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        // Get subcategories for this category
        $subkategoris = DB::table('sub_kategori')
            ->where('id_kategori', $id)
            ->get();

        // Build the response data
        $kategoriData = [
            'id' => $kategori->id,
            'nama' => $kategori->nama_indonesia,
            // Add other kategori fields as needed
            'subkategoris' => $subkategoris
        ];

        return response()->json([
            'success' => true,
            'data' => $kategoriData
        ]);
    }

    public function load(Request $request)
    {
        $query = DB::table('kategori')
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
                $editButton = '<a href="' . route('kelola_data.kategori.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.kategori.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('kelola_data.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama kategori (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama kategori (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            DB::table('kategori')->insert([
                'nama_arab' => $request->nama_arab,
                'nama_indonesia' => $request->nama_indonesia,
            ]);

            return redirect()->route('kelola_data.kategori.index')
                ->with([
                    'message' => 'Kategori berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan kategori: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $kategori = DB::table('kategori')->find($decryptedId);

        return view('kelola_data.kategori.create', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'nama_arab' => 'required|string|max:255',
            'nama_indonesia' => 'required|string|max:255',
        ], [
            'nama_arab.required' => 'Nama kategori (Arab) wajib diisi',
            'nama_arab.string' => 'Nama (Arab) harus berupa teks',
            'nama_arab.max' => 'Nama (Arab) tidak boleh lebih dari 255 karakter',
            'nama_indonesia.required' => 'Nama kategori (Indonesia) wajib diisi',
            'nama_indonesia.string' => 'Nama (Indonesia) harus berupa teks',
            'nama_indonesia.max' => 'Nama (Indonesia) tidak boleh lebih dari 255 karakter',
        ]);

        try {
            $affected = DB::table('kategori')
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

            return redirect()->route('kelola_data.kategori.index')
                ->with([
                    'message' => 'Data kategori berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data kategori: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $kategori = DB::table('kategori')->find($decryptedId);

            if ($kategori) {
                DB::table('kategori')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori.'
            ], 500);
        }
    }
}
