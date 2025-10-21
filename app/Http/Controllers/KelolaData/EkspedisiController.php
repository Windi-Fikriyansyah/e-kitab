<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EkspedisiController extends Controller
{
    public function index()
    {
        return view('kelola_data.ekspedisi.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('ekspedisi')
            ->select(['id', 'nama_ekspedisi', 'ekspedisi_logo']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_ekspedisi', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.ekspedisi.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.ekspedisi.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        return view('kelola_data.ekspedisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255',
            'ekspedisi_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_ekspedisi.required' => 'Nama ekspedisi wajib diisi',
            'nama_ekspedisi.string' => 'Nama ekspedisi harus berupa teks',
            'nama_ekspedisi.max' => 'Nama ekspedisi tidak boleh lebih dari 255 karakter',
            'ekspedisi_logo.image' => 'File harus berupa gambar',
            'ekspedisi_logo.mimes' => 'Format gambar yang diterima: jpeg, png, jpg, gif',
            'ekspedisi_logo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            $data = [
                'nama_ekspedisi' => $request->nama_ekspedisi,
            ];

            if ($request->hasFile('ekspedisi_logo')) {
                $filename = time() . '_' . $request->file('ekspedisi_logo')->getClientOriginalName();
                $path = $request->file('ekspedisi_logo')->storeAs('public/ekspedisi_logos', $filename);
                $data['ekspedisi_logo'] = 'ekspedisi_logos/' . $filename;
            }

            DB::table('ekspedisi')->insert($data);

            return redirect()->route('kelola_data.ekspedisi.index')
                ->with([
                    'message' => 'Ekspedisi berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan ekspedisi: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $ekspedisi = DB::table('ekspedisi')->find($decryptedId);

        return view('kelola_data.ekspedisi.create', compact('ekspedisi'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'nama_ekspedisi' => 'required|string|max:255',
            'ekspedisi_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_ekspedisi.required' => 'Nama ekspedisi wajib diisi',
            'nama_ekspedisi.string' => 'Nama ekspedisi harus berupa teks',
            'nama_ekspedisi.max' => 'Nama ekspedisi tidak boleh lebih dari 255 karakter',
            'ekspedisi_logo.image' => 'File harus berupa gambar',
            'ekspedisi_logo.mimes' => 'Format gambar yang diterima: jpeg, png, jpg, gif',
            'ekspedisi_logo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            $data = [
                'nama_ekspedisi' => $request->nama_ekspedisi,
            ];

            $ekspedisi = DB::table('ekspedisi')->find($decryptedId);

            if ($request->hasFile('ekspedisi_logo')) {
                // Delete old logo if exists
                if ($ekspedisi->ekspedisi_logo) {
                    Storage::delete('public/' . $ekspedisi->ekspedisi_logo);
                }

                $filename = time() . '_' . $request->file('ekspedisi_logo')->getClientOriginalName();
                $path = $request->file('ekspedisi_logo')->storeAs('public/ekspedisi_logos', $filename);
                $data['ekspedisi_logo'] = 'ekspedisi_logos/' . $filename;
            }

            $affected = DB::table('ekspedisi')
                ->where('id', $decryptedId)
                ->update($data);

            if ($affected === 0) {
                return redirect()->back()
                    ->withInput()
                    ->with([
                        'message' => 'Tidak ada perubahan data atau data tidak ditemukan',
                        'message_type' => 'warning',
                        'message_title' => 'Peringatan'
                    ]);
            }

            return redirect()->route('kelola_data.ekspedisi.index')
                ->with([
                    'message' => 'Data ekspedisi berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data ekspedisi: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $ekspedisi = DB::table('ekspedisi')->find($decryptedId);

            if ($ekspedisi) {
                DB::table('ekspedisi')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'ekspedisi berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ekspedisi.'
            ], 500);
        }
    }
}
