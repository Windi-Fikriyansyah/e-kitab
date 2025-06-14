<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ProfilePerusahaan extends Controller
{
    public function index()
    {
        $dataCount = DB::table('profile_perusahaan')->count();
        return view('kelola_data.profile_perusahaan.index', compact('dataCount'));
    }

    public function load(Request $request)
    {
        $query = DB::table('profile_perusahaan')
            ->select(['id', 'logo', 'nama_toko', 'no_wa', 'alamat', 'ig', 'fb', 'telegram', 'tokopedia', 'shoope']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                    ->orWhere('no_wa', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.profile_perusahaan.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.profile_perusahaan.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['logo', 'aksi'])
            ->make(true);
    }

    public function getSosmed(Request $request)
    {
        $profile = DB::table('profile_perusahaan')
            ->where('id', $request->id)
            ->first();

        if ($profile) {
            return response()->json([
                'success' => true,
                'data' => [
                    'ig' => $profile->ig,
                    'fb' => $profile->fb,
                    'telegram' => $profile->telegram,
                    'tokopedia' => $profile->tokopedia,
                    'shoope' => $profile->shoope
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }

    public function create()
    {
        return view('kelola_data.profile_perusahaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
            'alamat' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ig' => 'nullable|string|max:255',
            'fb' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'tokopedia' => 'nullable|string|max:255',
            'shoope' => 'nullable|string|max:255',
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi',
            'nama_toko.string' => 'Nama toko harus berupa teks',
            'nama_toko.max' => 'Nama toko maksimal 255 karakter',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi',
            'no_wa.string' => 'Nomor WhatsApp harus berupa teks',
            'no_wa.max' => 'Nomor WhatsApp maksimal 20 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'logo.max' => 'Ukuran gambar maksimal 2MB',
            'ig.string' => 'Instagram harus berupa teks',
            'ig.max' => 'Instagram maksimal 255 karakter',
            'fb.string' => 'Facebook harus berupa teks',
            'fb.max' => 'Facebook maksimal 255 karakter',
            'telegram.string' => 'Telegram harus berupa teks',
            'telegram.max' => 'Telegram maksimal 255 karakter',
            'tokopedia.string' => 'Tokopedia harus berupa teks',
            'tokopedia.max' => 'Tokopedia maksimal 255 karakter',
            'shoope.string' => 'Shopee harus berupa teks',
            'shoope.max' => 'Shopee maksimal 255 karakter',
        ]);

        $data = $request->except('_token', 'logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('profile_logos', 'public');
        }

        DB::table('profile_perusahaan')->insert($data);

        return redirect()->route('kelola_data.profile_perusahaan.index')
            ->with('message', 'Profile perusahaan berhasil ditambahkan.');
    }
    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $profile = DB::table('profile_perusahaan')->find($decryptedId);

        return view('kelola_data.profile_perusahaan.create', compact('profile'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);

        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
            'alamat' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ig' => 'nullable|string|max:255',
            'fb' => 'nullable|string|max:255',
            'telegram' => 'nullable|string|max:255',
            'tokopedia' => 'nullable|string|max:255',
            'shoope' => 'nullable|string|max:255',
        ], [
            'nama_toko.required' => 'Nama toko wajib diisi',
            'nama_toko.string' => 'Nama toko harus berupa teks',
            'nama_toko.max' => 'Nama toko maksimal 255 karakter',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi',
            'no_wa.string' => 'Nomor WhatsApp harus berupa teks',
            'no_wa.max' => 'Nomor WhatsApp maksimal 20 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'logo.max' => 'Ukuran gambar maksimal 2MB',
            'ig.string' => 'Instagram harus berupa teks',
            'ig.max' => 'Instagram maksimal 255 karakter',
            'fb.string' => 'Facebook harus berupa teks',
            'fb.max' => 'Facebook maksimal 255 karakter',
            'telegram.string' => 'Telegram harus berupa teks',
            'telegram.max' => 'Telegram maksimal 255 karakter',
            'tokopedia.string' => 'Tokopedia harus berupa teks',
            'tokopedia.max' => 'Tokopedia maksimal 255 karakter',
            'shoope.string' => 'Shopee harus berupa teks',
            'shoope.max' => 'Shopee maksimal 255 karakter',
        ]);

        $data = $request->except('_token', '_method', 'logo');
        $profile = DB::table('profile_perusahaan')->find($decryptedId);

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($profile->logo && \Storage::disk('public')->exists($profile->logo)) {
                \Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $request->file('logo')->store('profile_logos', 'public');
        }

        DB::table('profile_perusahaan')
            ->where('id', $decryptedId)
            ->update($data);

        return redirect()->route('kelola_data.profile_perusahaan.index')
            ->with('message', 'Profile perusahaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $profile = DB::table('profile_perusahaan')->find($decryptedId);

            if ($profile) {
                // Delete logo if exists
                if ($profile->logo && \Storage::disk('public')->exists($profile->logo)) {
                    \Storage::disk('public')->delete($profile->logo);
                }

                DB::table('profile_perusahaan')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile perusahaan berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus profile perusahaan.'
            ], 500);
        }
    }
}
