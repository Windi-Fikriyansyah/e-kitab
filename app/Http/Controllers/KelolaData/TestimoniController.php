<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
{
    public function index()
    {
        return view('pengaturan_web.testimoni.index');
    }


    public function load(Request $request)
    {
        $query = DB::table('testimoni')
            ->select(['id', 'nama_customer', 'foto_unboxing', 'caption', 'created_at']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_customer', 'like', "%{$search}%")
                    ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto_unboxing', function ($row) {
                if ($row->foto_unboxing) {
                    $url = asset('storage/testimoni/' . $row->foto_unboxing);
                    return '<img src="' . $url . '" class="logo-img" alt="Foto">';
                }
                return '<span class="badge bg-secondary">Tidak ada</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('pengaturan_web.testimoni.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('pengaturan_web.testimoni.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['foto_unboxing', 'aksi'])
            ->make(true);
    }


    public function create()
    {
        return view('pengaturan_web.testimoni.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'caption' => 'required|string|max:500',
            'foto_unboxing' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Simpan foto
        $fotoName = time() . '.' . $request->foto_unboxing->extension();
        $request->foto_unboxing->storeAs('public/testimoni', $fotoName);

        DB::table('testimoni')->insert([
            'nama_customer' => $request->nama_customer,
            'caption' => $request->caption,
            'foto_unboxing' => $fotoName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pengaturan_web.testimoni.index')
            ->with(['message' => 'Testimoni berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);

        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'caption' => 'required|string|max:500',
            'foto_unboxing' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'nama_customer' => $request->nama_customer,
            'caption' => $request->caption,
            'updated_at' => now(),
        ];

        if ($request->hasFile('foto_unboxing')) {
            $fotoName = time() . '.' . $request->foto_unboxing->extension();
            $request->foto_unboxing->storeAs('public/testimoni', $fotoName);
            $data['foto_unboxing'] = $fotoName;
        }

        DB::table('testimoni')->where('id', $id)->update($data);

        return redirect()->route('pengaturan_web.testimoni.index')
            ->with(['message' => 'Testimoni berhasil diupdate']);
    }


    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $testimoni = DB::table('testimoni')->find($decryptedId);

        return view('pengaturan_web.testimoni.create', compact('testimoni'));
    }




    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $testimoni = DB::table('testimoni')->find($decryptedId);

            if ($testimoni) {
                // Hapus file foto jika ada
                if ($testimoni->foto_unboxing && Storage::exists('public/testimoni/' . $testimoni->foto_unboxing)) {
                    Storage::delete('public/testimoni/' . $testimoni->foto_unboxing);
                }

                // Hapus data dari database
                DB::table('testimoni')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Testimoni berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus testimoni.'
            ], 500);
        }
    }
}
