<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageKitService;

class TestimoniController extends Controller
{
    protected $imageKitService;

    public function __construct(ImageKitService $imageKitService)
    {
        $this->imageKitService = $imageKitService;
    }

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
                    $fotoData = json_decode($row->foto_unboxing, true);
                    $url = (is_array($fotoData) && isset($fotoData['url'])) ? $fotoData['url'] : asset('storage/testimoni/' . $row->foto_unboxing);
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
            'foto_unboxing' => 'required|image|mimes:webp|max:2048',
        ], [
            'foto_unboxing.mimes' => 'Format gambar yang diperbolehkan: webp',
        ]);

        $image = $request->file('foto_unboxing');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Upload to ImageKit
        $upload = $this->imageKitService->upload($image, $imageName, 'testimoni');
        
        $fotoData = null;
        if (isset($upload->result) && $upload->result) {
            $fotoData = json_encode([
                'url' => $upload->result->url,
                'file_id' => $upload->result->fileId
            ]);
        }

        DB::table('testimoni')->insert([
            'nama_customer' => $request->nama_customer,
            'caption' => $request->caption,
            'foto_unboxing' => $fotoData,
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
            'foto_unboxing' => 'nullable|image|mimes:webp|max:2048',
        ], [
            'foto_unboxing.mimes' => 'Format gambar yang diperbolehkan: webp',
        ]);

        $existingTestimoni = DB::table('testimoni')->find($id);

        $data = [
            'nama_customer' => $request->nama_customer,
            'caption' => $request->caption,
            'updated_at' => now(),
        ];

        if ($request->hasFile('foto_unboxing')) {
            // Hapus foto lama jika ada di ImageKit
            if ($existingTestimoni->foto_unboxing) {
                $oldFotoData = json_decode($existingTestimoni->foto_unboxing, true);
                if (is_array($oldFotoData) && isset($oldFotoData['file_id'])) {
                    $this->imageKitService->delete($oldFotoData['file_id']);
                } else {
                    // Fallback local delete
                    Storage::delete('public/testimoni/' . $existingTestimoni->foto_unboxing);
                }
            }

            $image = $request->file('foto_unboxing');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Upload to ImageKit
            $upload = $this->imageKitService->upload($image, $imageName, 'testimoni');
            
            if (isset($upload->result) && $upload->result) {
                $data['foto_unboxing'] = json_encode([
                    'url' => $upload->result->url,
                    'file_id' => $upload->result->fileId
                ]);
            }
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
                if ($testimoni->foto_unboxing) {
                    $fotoData = json_decode($testimoni->foto_unboxing, true);
                    if (is_array($fotoData) && isset($fotoData['file_id'])) {
                        // Delete from ImageKit
                        $this->imageKitService->delete($fotoData['file_id']);
                    } else {
                        // Delete from local storage
                        if (Storage::exists('public/testimoni/' . $testimoni->foto_unboxing)) {
                            Storage::delete('public/testimoni/' . $testimoni->foto_unboxing);
                        }
                    }
                }

                // Hapus data dari database
                DB::table('testimoni')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Testimoni dan gambar berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus testimoni: ' . $e->getMessage()
            ], 500);
        }
    }
}
