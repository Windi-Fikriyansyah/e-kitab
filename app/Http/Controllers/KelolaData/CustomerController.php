<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $dataCount = DB::table('customer')->count();
        return view('kelola_data.customer.index', compact('dataCount'));
    }

    public function load(Request $request)
    {
        $query = DB::table('customer')
            ->select(['id', 'nama', 'no_hp', 'alamat']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.customer.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.customer.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        return view('kelola_data.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20|unique:customer,no_hp',
            'alamat' => 'required|string',
        ], [
            'nama.required' => 'Nama customer wajib diisi',
            'nama.string' => 'Nama harus berupa teks',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.string' => 'Nomor HP harus berupa teks',
            'no_hp.max' => 'Nomor HP tidak boleh lebih dari 20 karakter',
            'no_hp.unique' => 'Nomor HP ini sudah terdaftar',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
        ]);

        try {
            DB::table('customer')->insert([
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('kelola_data.customer.index')
                ->with([
                    'message' => 'Customer berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan customer: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function edit($id)
    {

        $decryptedId = Crypt::decrypt($id);
        $customer = DB::table('customer')->find($decryptedId);

        return view('kelola_data.customer.create', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20|unique:customer,no_hp,' . $decryptedId,
            'alamat' => 'required|string',
        ], [
            'nama.required' => 'Nama customer wajib diisi',
            'nama.string' => 'Nama harus berupa teks',
            'nama.max' => 'Nama tidak boleh lebih dari 255 karakter',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'no_hp.string' => 'Nomor HP harus berupa teks',
            'no_hp.max' => 'Nomor HP tidak boleh lebih dari 20 karakter',
            'no_hp.unique' => 'Nomor HP ini sudah digunakan oleh customer lain',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
        ]);

        try {
            $decryptedId = Crypt::decrypt($id);

            $affected = DB::table('customer')
                ->where('id', $decryptedId)
                ->update([
                    'nama' => $request->nama,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat,
                    'updated_at' => now(),
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

            return redirect()->route('kelola_data.customer.index')
                ->with([
                    'message' => 'Data customer berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data customer: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $profile = DB::table('customer')->find($decryptedId);

            if ($profile) {


                DB::table('customer')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'customer berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus customer.'
            ], 500);
        }
    }
}
