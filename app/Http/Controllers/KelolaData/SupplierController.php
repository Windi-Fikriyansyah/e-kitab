<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {

        return view('kelola_data.supplier.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('supplier')
            ->select(['id', 'nama_supplier', 'alamat', 'telepon', 'email']);

        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $editButton = '<a href="' . route('kelola_data.supplier.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning right-gap"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.supplier.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $editButton . $deleteButton;
            })
            ->rawColumns(['logo', 'aksi'])
            ->make(true);
    }



    public function create()
    {
        return view('kelola_data.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi',
            'nama_supplier.string' => 'Nama supplier harus berupa teks',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.string' => 'Telepon harus berupa teks',
            'telepon.max' => 'Telepon maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
        ]);

        DB::table('supplier')->insert([
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'created_at' => now()
        ]);

        return redirect()->route('kelola_data.supplier.index')
            ->with('message', 'Supplier berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $supplier = DB::table('supplier')->find($decryptedId);

        return view('kelola_data.supplier.create', ['supplier' => $supplier]);
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);

        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi',
            'nama_supplier.string' => 'Nama supplier harus berupa teks',
            'nama_supplier.max' => 'Nama supplier maksimal 255 karakter',
            'alamat.required' => 'Alamat wajib diisi',
            'alamat.string' => 'Alamat harus berupa teks',
            'telepon.required' => 'Telepon wajib diisi',
            'telepon.string' => 'Telepon harus berupa teks',
            'telepon.max' => 'Telepon maksimal 20 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
        ]);

        DB::table('supplier')
            ->where('id', $decryptedId)
            ->update([
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'updated_at' => now(),
            ]);

        return redirect()->route('kelola_data.supplier.index')
            ->with('message', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $profile = DB::table('supplier')->find($decryptedId);

            if ($profile) {


                DB::table('supplier')->where('id', $decryptedId)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Supplier berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Supplier.'
            ], 500);
        }
    }
}
