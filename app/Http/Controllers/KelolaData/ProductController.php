<?php

namespace App\Http\Controllers\KelolaData;
namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        $products = Product::all();
        return view('kelola_data.products.index', compact('products'));
    }

    public function load(Request $request)
    {
        // Ambil data produk dari database untuk DataTables
        $query = DB::table('products')
                    ->select(['id', 'barcode', 'name', 'category', 'selling_price','purchase_price','photo', 'stock', 'satuan']);

                    if (!empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('barcode', 'like', "%{$search}%")
                              ->orWhere('name', 'like', "%{$search}%")
                              ->orWhere('category', 'like', "%{$search}%");
                        });
                    }

                    // Return data dalam format DataTables
                    return DataTables::of($query)
                        ->addIndexColumn() // Tambahkan kolom index
                        ->addColumn('aksi', function ($row) {
                            $editButton = '<a href="' . route('kelola_data.products.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-primary">Edit</a>';
                            $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.products.destroy', Crypt::encrypt($row->id)) . '">Hapus</button>';
                            return $editButton . ' ' . $deleteButton;
                        })
                        ->editColumn('photo', function ($row) {
                            return $row->photo
                                ? '<img src="' . asset('storage/' . $row->photo) . '" style="width:50px; height:50px;">'
                                : 'No Photo';
                        })
                        ->rawColumns(['aksi', 'photo']) // Izinkan HTML untuk kolom tertentu
                        ->make(true);
    }


    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.products.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|unique:products,barcode',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'satuan' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'barcode.required' => 'Barcode harus diisi.',
            'barcode.unique' => 'Barcode ini sudah digunakan, silakan masukkan barcode lain.',
            'photo.image' => 'Foto harus berupa file gambar.',
            'photo.mimes' => 'Foto hanya boleh memiliki format jpeg, png, jpg, atau gif.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        Product::create($data);

        return redirect()->route('kelola_data.products.index')->with('message', 'Produk berhasil ditambahkan.');
    }

    // Tampilkan form edit produk
    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Cari produk berdasarkan ID atau gagal jika tidak ditemukan
        $product = Product::findOrFail($decryptedId);

        // Tampilkan view untuk mengedit produk
        return view('kelola_data.products.create', compact('product'));
    }


    // Update produk
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'barcode' => 'required|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'satuan' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['barcode', 'name', 'category', 'purchase_price', 'selling_price', 'stock', 'satuan']);

    // Jika ada foto baru yang diunggah
    if ($request->hasFile('photo')) {
        // Hapus foto lama jika ada
        if ($product->photo && file_exists(storage_path('app/public/' . $product->photo))) {
            unlink(storage_path('app/public/' . $product->photo));
        }

        // Simpan foto baru
        $photoPath = $request->file('photo')->store('photos', 'public');
        $data['photo'] = $photoPath;
    }

    // Perbarui produk
    $product->update($data);

        return redirect()->route('kelola_data.products.index')->with('message', 'Produk berhasil diubah.');
    }

    // Hapus produk
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $product = Product::findOrFail($decryptedId);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk.'
            ], 500);
        }
    }

}
