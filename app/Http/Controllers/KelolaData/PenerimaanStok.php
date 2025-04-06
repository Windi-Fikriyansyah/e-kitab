<?php

namespace App\Http\Controllers\KelolaData;
namespace App\Http\Controllers\KelolaData;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PenerimaanStok_m;
use Illuminate\Http\Request;
use App\Models\Product;

class PenerimaanStok extends Controller
{
    // Tampilkan daftar produk
    public function index()
    {
        $PS = PenerimaanStok_m::all();
        return view('kelola_data.penerimaan_stok.index', compact('PS'));
    }

    public function load(Request $request)
{
    // Ambil data produk dari database
    $query = DB::table('penerimaanstok')
    ->select('penerimaanstok.*', DB::raw('penerimaanstok.created_at as tanggal_input')) // Memilih semua kolom dari tabel dan menambahkan alias untuk kolom created_at
    ->orderBy('penerimaanstok.created_at', 'desc') // Mengurutkan data berdasarkan waktu input terbaru
    ->get();



    // Kembalikan data dalam format DataTables
    return DataTables::of($query)
        ->addColumn('tanggal_input', function ($row) {
            return \Carbon\Carbon::parse($row->tanggal_input)->format('Y-m-d'); // Ubah format sesuai kebutuhan
        }) // Render kolom 'aksi' sebagai HTML
        ->make(true);
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where('barcode', 'LIKE', '%' . $search . '%')
            ->orWhere('name', 'LIKE', '%' . $search . '%')
            ->get(['id', 'barcode', 'name', 'category', 'purchase_price', 'selling_price', 'stock']);

        return response()->json($products);
    }
    // ProductController.php
    public function getProductByBarcode($barcode)
{
    $product = Product::where('barcode', $barcode)->first();

    if ($product) {
        return response()->json([
            'name' => $product->name,
            'category' => $product->category,
            'purchase_price' => $product->purchase_price,
            'selling_price' => $product->selling_price,
            'stock' => $product->stock,
        ]);
    }

    return response()->json(['message' => 'Produk tidak ditemukan'], 404);
}


    // Tampilkan form tambah produk
    public function create()
    {
        return view('kelola_data.penerimaan_stok.create');
    }

    // Simpan produk baru
    public function store(Request $request)
{
    // Validate the input data
    $request->validate([
        'barcode' => 'required', // Ensure barcode is selected
        'tambah_stock' => 'required|numeric|min:1',
    ]);

    // Get the product by barcode
    $product = Product::where('barcode', $request->barcode)->first();

    if ($product) {
        // Add the new stock to the existing stock
        $product->stock += $request->tambah_stock;
        $product->save();

        // Save the stock receipt and store the product id
        $penerimaanStok = new PenerimaanStok_m();
        $penerimaanStok->id_product = $product->id;
        $penerimaanStok->barcode = $product->barcode;
        $penerimaanStok->nama_produk = $product->name;
        $penerimaanStok->kategori = $product->category;
        $penerimaanStok->harga_jual = $product->purchase_price;
        $penerimaanStok->harga_beli = $product->selling_price;
        $penerimaanStok->keterangan = $product->keterangan; // Store the product's id
        $penerimaanStok->tambah_stok = $request->tambah_stock;
        $penerimaanStok->save();

        return redirect()->route('kelola_data.PenerimaanStok.index')->with('message', 'Stok berhasil diterima');
    } else {
        return redirect()->route('kelola_data.PenerimaanStok.index')->with('message', 'Produk tidak ditemukan');
    }
}



    // Tampilkan form edit produk
    public function edit($id)
    {
        // Dekripsi ID yang terenkripsi
        $decryptedId = Crypt::decrypt($id);

        // Cari produk berdasarkan ID atau gagal jika tidak ditemukan
        $product = Product::findOrFail($decryptedId);

        // Tampilkan view untuk mengedit produk
        return view('kelola_data.PenerimaanStok.create', compact('product'));
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
        ]);

        $product->update($request->all());

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
