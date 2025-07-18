<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProdukController extends Controller
{
    public function index()
    {
        $kategoris = DB::table('kategori')->select('id', 'nama_arab', 'nama_indonesia')->get();
        $penulis = DB::table('penulis')->select('id', 'nama_arab', 'nama_indonesia')->get();
        $penerbits = DB::table('penerbit')->select('id', 'nama_arab', 'nama_indonesia')->get();
        $suppliers = DB::table('supplier')->select('id', 'nama_supplier', 'telepon')->get();
        return view('kelola_data.produk.index', compact('kategoris', 'penerbits', 'suppliers', 'penulis'));
    }

    public function load(Request $request)
    {
        // dd($request->all());
        $query = DB::table('produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                'supplier.nama_supplier as supplier',
                'produk.stok',
                'produk.harga_modal',
                'produk.images',
                'produk.harga_jual'
            ]);

        // Filter untuk setiap kolom
        if ($request->has('columns')) {
            foreach ($request->columns as $column) {
                if ($column['search']['value'] != '') {
                    $columnIndex = $column['data'];
                    $searchValue = $column['search']['value'];

                    switch ($columnIndex) {
                        case '0':
                            $query->where('kd_produk', 'like', "%{$searchValue}%");
                            break;
                        case '1': // Judul
                            $query->where('judul', 'like', "%{$searchValue}%");
                            break;
                        case '2': // penulis
                            $query->where('penulis', $searchValue);
                            break;
                        case '3': // Kategori
                            $query->where('kategori', $searchValue);
                            break;
                        case '4': // Penerbit
                            $query->where('penerbit', $searchValue);
                            break;
                        case '5': // Supplier
                            $query->where('supplier.id', $searchValue);
                            break;
                        case '6': // Stok
                            $query->where('stok', 'like', "%{$searchValue}%");
                            break;
                    }
                }
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $viewButton = '<a href="' . route('kelola_data.produk.show', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-info me-1"><i class="fas fa-eye"></i></a>';
                $editButton = '<a href="' . route('kelola_data.produk.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>';
                $barcodeButton = '<button class="btn btn-sm btn-primary me-1 barcode-btn" data-kd="' . $row->kd_produk . '" data-judul="' . $row->judul . '"><i class="fas fa-barcode"></i></button>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.produk.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $viewButton . $editButton . $barcodeButton . $deleteButton;
            })
            ->rawColumns(['aksi', 'images'])
            ->make(true);
    }

    public function addColumn(Request $request)
    {
        $request->validate([
            'column_name' => 'required|string|max:255',
            'data_type' => 'required|in:string,integer,text,boolean,date,datetime,decimal'
        ]);

        try {
            $columnName = $request->column_name;
            $dataTypeInput = $request->data_type;

            // Pemetaan tipe data Laravel ke MySQL
            $typeMap = [
                'string' => 'VARCHAR(255)',
                'integer' => 'INT',
                'text' => 'TEXT',
                'boolean' => 'TINYINT(1)',
                'date' => 'DATE',
                'datetime' => 'DATETIME',
                'decimal' => 'DECIMAL(10,2)',
            ];

            $dataType = $typeMap[$dataTypeInput];

            // Tambahkan kolom ke tabel produk
            DB::statement("ALTER TABLE produk ADD COLUMN {$columnName} {$dataType}");

            // Tambahkan kolom ke tabel produk_indo
            DB::statement("ALTER TABLE produk_indo ADD COLUMN {$columnName}_indo {$dataType}");

            return response()->json([
                'success' => true,
                'message' => 'Kolom berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kolom: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getDynamicColumns()
    {
        try {
            // Dapatkan daftar kolom dari tabel produk
            $columns = DB::getSchemaBuilder()->getColumnListing('produk');

            // Filter kolom yang tidak ingin ditampilkan
            $excludedColumns = ['id', 'kd_produk', 'sub_kategori', 'images', 'penulis', 'created_at', 'updated_at', 'deleted_at'];
            $dynamicColumns = array_diff($columns, $excludedColumns);

            return response()->json([
                'success' => true,
                'columns' => array_values($dynamicColumns)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan daftar kolom: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);

            // Ambil data produk dengan join
            $produk = DB::table('produk')
                ->join('produk_indo', 'produk.id', '=', 'produk_indo.id_produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->select('produk.*', 'produk_indo.*', 'supplier.nama_supplier', 'supplier.telepon')
                ->where('produk.id', $decryptedId)
                ->first();

            if (!$produk) {
                return redirect()->route('kelola_data.produk.index')
                    ->with([
                        'message' => 'Produk tidak ditemukan',
                        'message_type' => 'danger',
                        'message_title' => 'Error'
                    ]);
            }

            // Dapatkan daftar kolom dinamis dari tabel produk
            $allColumns = DB::getSchemaBuilder()->getColumnListing('produk');

            // Filter kolom yang tidak ingin ditampilkan sebagai kolom dinamis
            $excludedColumns = [
                'id',
                'kd_produk',
                'judul',
                'kategori',
                'sub_kategori',
                'penerbit',
                'cover',
                'kertas',
                'kualitas',
                'harakat',
                'supplier',
                'halaman',
                'berat',
                'ukuran',
                'harga_modal',
                'harga_jual',
                'stok',
                'penulis',
                'images',
                'created_at',
                'updated_at',
                'deleted_at'
            ];

            $dynamicColumns = array_diff($allColumns, $excludedColumns);

            return view('kelola_data.produk.show', compact('produk', 'dynamicColumns'));
        } catch (\Exception $e) {
            return redirect()->route('kelola_data.produk.index')
                ->with([
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function create()
    {
        return view('kelola_data.produk.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'judul_arab' => 'required|string|max:255',
            'judul_indonesia' => 'required|string|max:255',
            'kategori' => 'required',
            'kategori_indonesia' => 'required',
            'sub_kategori' => 'required',
            'sub_kategori_indonesia' => 'required',
            'penerbit' => 'required',
            'penerbit_indonesia' => 'required',
            'cover' => 'required',
            'cover_indonesia' => 'required',
            'kertas' => 'required',
            'kertas_indonesia' => 'required',
            'kualitas' => 'required',
            'kualitas_indonesia' => 'required',
            'harakat' => 'required',
            'harakat_indonesia' => 'required',
            'penulis' => 'required',
            'penulis_indonesia' => 'required',
            'supplier' => 'required',
            'halaman' => 'required|integer',
            'berat' => 'required',
            'ukuran' => 'required|string',
            'harga_modal' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'judul_arab.required' => 'Judul produk (Arab) wajib diisi',
            'judul_indonesia.required' => 'Judul produk (Indonesia) wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'sub_kategori.required' => 'Sub Kategori wajib dipilih',
            'penerbit.required' => 'Penerbit wajib dipilih',
            'cover.required' => 'Cover wajib dipilih',
            'kertas.required' => 'Kertas wajib dipilih',
            'kualitas.required' => 'Kualitas wajib dipilih',
            'harakat.required' => 'Harakat wajib dipilih',
            'penulis.required' => 'Penulis wajib dipilih',
            'supplier.required' => 'Supplier wajib dipilih',
            'halaman.required' => 'Jumlah halaman wajib diisi',
            'berat.required' => 'Berat produk wajib diisi',
            'ukuran.required' => 'Ukuran produk wajib diisi',
            'harga_modal.required' => 'Harga modal wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'images.*.max' => 'Ukuran gambar maksimal 2MB'
        ]);

        try {
            DB::beginTransaction();

            // Generate kode produk
            $tanggal = date('Ymd'); // Format: 20250613
            $lastProductToday = DB::table('produk')
                ->whereDate('created_at', now()->toDateString())
                ->orderBy('id', 'desc')
                ->first();

            $lastIdToday = $lastProductToday ? intval(substr($lastProductToday->kd_produk, -5)) : 0;
            $kd_produk = 'PR' . $tanggal . str_pad($lastIdToday + 1, 5, '0', STR_PAD_LEFT);


            $imageNames = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Generate unique filename
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Store the file in storage/app/public/products
                    $path = $image->storeAs('public/products', $imageName);

                    // Add to array of image names
                    $imageNames[] = $imageName;
                }
            }
            // Data untuk tabel produk
            $produkData = [
                'kd_produk' => $kd_produk,
                'judul' => $request->judul_arab,
                'kategori' => $request->kategori,
                'sub_kategori' => $request->sub_kategori,
                'penerbit' => $request->penerbit,
                'cover' => $request->cover,
                'kertas' => $request->kertas,
                'kualitas' => $request->kualitas,
                'harakat' => $request->harakat,
                'penulis' => $request->penulis,
                'supplier' => $request->supplier,
                'halaman' => $request->halaman,
                'berat' => $request->berat,
                'ukuran' => $request->ukuran,
                'harga_modal' => $request->harga_modal,
                'harga_jual' => $request->harga_jual,
                'stok' => $request->stok,
                'images' => !empty($imageNames) ? json_encode($imageNames) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Tambahkan kolom dinamis ke data produk
            $dynamicColumns = DB::getSchemaBuilder()->getColumnListing('produk');
            foreach ($dynamicColumns as $column) {
                if ($column === 'images') {
                    continue; // Hindari menimpa nilai images yang sudah diproses
                }
                if ($request->has($column)) {
                    $produkData[$column] = $request->$column;
                }
            }

            // Simpan ke tabel produk
            $produkId = DB::table('produk')->insertGetId($produkData);

            // Data untuk tabel produk_indo
            $produkIndoData = [
                'id_produk' => $produkId,
                'judul_indo' => $request->judul_indonesia,
                'kategori_indo' => $request->kategori_indonesia,
                'sub_kategori_indo' => $request->sub_kategori_indonesia,
                'penerbit_indo' => $request->penerbit_indonesia,
                'cover_indo' => $request->cover_indonesia,
                'kertas_indo' => $request->kertas_indonesia,
                'kualitas_indo' => $request->kualitas_indonesia,
                'harakat_indo' => $request->harakat_indonesia,
                'penulis_indo' => $request->penulis_indonesia,
            ];

            // Tambahkan kolom dinamis ke data produk_indo
            foreach ($dynamicColumns as $column) {
                if ($request->has($column . '_indo')) {
                    $produkIndoData[$column . '_indo'] = $request->{$column . '_indo'};
                }
            }

            // Simpan ke tabel produk_indo
            DB::table('produk_indo')->insert($produkIndoData);

            DB::commit();

            return redirect()->route('kelola_data.produk.index')
                ->with([
                    'message' => 'Produk berhasil ditambahkan',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal menambahkan produk: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }

    public function update(Request $request, $id)
    {
        $decryptedId = Crypt::decrypt($id);

        $request->validate([
            'judul_arab' => 'required|string|max:255',
            'judul_indonesia' => 'required|string|max:255',
            'kategori' => 'required',
            'kategori_indonesia' => 'required',
            'penerbit' => 'required',
            'penerbit_indonesia' => 'required',
            'cover' => 'required',
            'cover_indonesia' => 'required',
            'kertas' => 'required',
            'kertas_indonesia' => 'required',
            'kualitas' => 'required',
            'kualitas_indonesia' => 'required',
            'harakat' => 'required',
            'harakat_indonesia' => 'required',
            'penulis' => 'required',
            'penulis_indonesia' => 'required',
            'supplier' => 'required',
            'halaman' => 'required|integer',
            'berat' => 'required',
            'ukuran' => 'required|string',
            'harga_modal' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images.*' => 'sometimes|string'
        ], [
            'judul_arab.required' => 'Judul produk (Arab) wajib diisi',
            'judul_indonesia.required' => 'Judul produk (Indonesia) wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'penerbit.required' => 'Penerbit wajib dipilih',
            'cover.required' => 'Cover wajib dipilih',
            'kertas.required' => 'Kertas wajib dipilih',
            'kualitas.required' => 'Kualitas wajib dipilih',
            'harakat.required' => 'Harakat wajib dipilih',
            'penulis.required' => 'Penulis wajib dipilih',
            'supplier.required' => 'Supplier wajib dipilih',
            'halaman.required' => 'Jumlah halaman wajib diisi',
            'berat.required' => 'Berat produk wajib diisi',
            'ukuran.required' => 'Ukuran produk wajib diisi',
            'harga_modal.required' => 'Harga modal wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
            'images.*.max' => 'Ukuran gambar maksimal 2MB'
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah produk ada
            $existingProduct = DB::table('produk')->where('id', $decryptedId)->first();
            if (!$existingProduct) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with([
                        'message' => 'Produk tidak ditemukan',
                        'message_type' => 'danger',
                        'message_title' => 'Error'
                    ]);
            }

            $imageNames = [];

            // Jika ada gambar yang sudah ada, tambahkan ke array
            if ($request->has('existing_images')) {
                $imageNames = $request->existing_images;
            } elseif ($existingProduct->images) {
                $imageNames = json_decode($existingProduct->images, true);
            }

            // Hapus gambar yang dipilih
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageToDelete) {
                    if (($key = array_search($imageToDelete, $imageNames)) !== false) {
                        // Hapus file dari storage
                        Storage::delete('public/products/' . $imageToDelete);
                        // Hapus dari array
                        unset($imageNames[$key]);
                    }
                }
                $imageNames = array_values($imageNames); // Reindex array
            }

            // Tambahkan gambar baru
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Generate nama unik untuk gambar
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Simpan gambar
                    $path = $image->storeAs('public/products', $imageName);

                    // Tambahkan ke array
                    $imageNames[] = $imageName;
                }
            }
            // Siapkan data untuk update tabel produk
            $produkUpdateData = [
                'judul' => $request->judul_arab,
                'kategori' => $request->kategori,
                'penerbit' => $request->penerbit,
                'cover' => $request->cover,
                'kertas' => $request->kertas,
                'kualitas' => $request->kualitas,
                'harakat' => $request->harakat,
                'penulis' => $request->penulis,
                'supplier' => $request->supplier,
                'halaman' => $request->halaman,
                'berat' => $request->berat,
                'ukuran' => $request->ukuran,
                'harga_modal' => $request->harga_modal,
                'harga_jual' => $request->harga_jual,
                'stok' => $request->stok,
                'images' => !empty($imageNames) ? json_encode($imageNames) : null,
                'updated_at' => now(),
            ];

            // Tambahkan kolom dinamis untuk tabel produk
            $dynamicColumns = DB::getSchemaBuilder()->getColumnListing('produk');
            foreach ($dynamicColumns as $column) {
                // Skip kolom-kolom sistem dan yang sudah ada
                if (in_array($column, ['id', 'kd_produk', 'created_at', 'updated_at', 'images'])) {
                    continue;
                }

                if ($request->has($column) && !array_key_exists($column, $produkUpdateData)) {
                    $produkUpdateData[$column] = $request->$column;
                }
            }

            // Update tabel produk
            $affected = DB::table('produk')
                ->where('id', $decryptedId)
                ->update($produkUpdateData);

            // Siapkan data untuk update tabel produk_indo
            $produkIndoUpdateData = [
                'judul_indo' => $request->judul_indonesia,
                'kategori_indo' => $request->kategori_indonesia,
                'penerbit_indo' => $request->penerbit_indonesia,
                'cover_indo' => $request->cover_indonesia,
                'kertas_indo' => $request->kertas_indonesia,
                'kualitas_indo' => $request->kualitas_indonesia,
                'harakat_indo' => $request->harakat_indonesia,
                'penulis_indo' => $request->penulis_indonesia,
            ];

            // Tambahkan kolom dinamis untuk tabel produk_indo
            foreach ($dynamicColumns as $column) {
                $indoColumn = $column . '_indo';
                if ($request->has($indoColumn) && !array_key_exists($indoColumn, $produkIndoUpdateData)) {
                    $produkIndoUpdateData[$indoColumn] = $request->$indoColumn;
                }
            }

            // Cek apakah record produk_indo sudah ada
            $existingProdukIndo = DB::table('produk_indo')->where('id_produk', $decryptedId)->first();

            if ($existingProdukIndo) {
                // Update jika sudah ada
                DB::table('produk_indo')
                    ->where('id_produk', $decryptedId)
                    ->update($produkIndoUpdateData);
            } else {
                // Insert jika belum ada
                $produkIndoUpdateData['id_produk'] = $decryptedId;
                DB::table('produk_indo')->insert($produkIndoUpdateData);
            }

            DB::commit();

            return redirect()->route('kelola_data.produk.index')
                ->with([
                    'message' => 'Data produk berhasil diperbarui',
                    'message_type' => 'success',
                    'message_title' => 'Sukses'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with([
                    'message' => 'Gagal memperbarui data produk: ' . $e->getMessage(),
                    'message_type' => 'danger',
                    'message_title' => 'Error'
                ]);
        }
    }
    public function edit($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $produk = DB::table('produk')
            ->join('produk_indo', 'produk.id', '=', 'produk_indo.id_produk')
            ->join('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select('produk.*', 'produk_indo.*', 'supplier.nama_supplier', 'supplier.telepon')
            ->where('produk.id', $decryptedId)
            ->first();

        return view('kelola_data.produk.create', compact('produk'));
    }



    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $decryptedId = Crypt::decrypt($id);

            // Hapus data dari tabel produk_indo terlebih dahulu
            DB::table('produk_indo')->where('id_produk', $decryptedId)->delete();

            // Hapus data dari tabel produk
            $deleted = DB::table('produk')->where('id', $decryptedId)->delete();

            DB::commit();

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
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
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getSubKategori(Request $request)
    {
        $term = $request->q;
        $kategori = $request->kategori;

        $query = DB::table('sub_kategori');

        if ($kategori) {
            // Cari kategori berdasarkan nama_arab
            $kategoriData = DB::table('kategori')
                ->where('nama_arab', $kategori)
                ->first();

            if ($kategoriData) {
                $query->where('id_kategori', $kategoriData->id);
            }
        }

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nama_arab', 'like', '%' . $term . '%')
                    ->orWhere('nama_indonesia', 'like', '%' . $term . '%');
            });
        }

        $subKategoris = $query->get();

        $results = [];
        foreach ($subKategoris as $subKategori) {
            $results[] = [
                'id' => $subKategori->nama_arab,
                'text' => $subKategori->nama_arab . ' | ' . $subKategori->nama_indonesia,
                'nama_arab' => $subKategori->nama_arab,
                'nama_indonesia' => $subKategori->nama_indonesia
            ];
        }

        return response()->json($results);
    }

    public function getpenerbit(Request $request)
    {
        $search = $request->q;

        $penerbit = DB::table('penerbit')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $penerbit->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getcover(Request $request)
    {
        $search = $request->q;

        $cover = DB::table('cover')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $cover->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getkertas(Request $request)
    {
        $search = $request->q;

        $kertas = DB::table('kertas')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $kertas->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getkualitas(Request $request)
    {
        $search = $request->q;

        $kualitas = DB::table('kualitas')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $kualitas->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getharakat(Request $request)
    {
        $search = $request->q;

        $harakat = DB::table('harakat')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $harakat->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getpenulis(Request $request)
    {
        $search = $request->q;

        $penulis = DB::table('penulis')
            ->select('id', 'nama_arab', 'nama_indonesia')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_arab', 'LIKE', "%{$search}%")
                    ->orWhere('nama_indonesia', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $penulis->map(function ($item) {
            return [
                'id' => $item->nama_arab,
                'nama_arab' => $item->nama_arab,
                'nama_indonesia' => $item->nama_indonesia,
                'text' => $item->nama_arab . ' | ' . $item->nama_indonesia
            ];
        });

        return response()->json($data);
    }

    public function getsupplier(Request $request)
    {
        $search = $request->q;

        $supplier = DB::table('supplier')
            ->select('id', 'nama_supplier', 'alamat', 'telepon')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama_supplier', 'LIKE', "%{$search}%")
                    ->orWhere('telepon', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $supplier->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama_supplier . ' | ' . $item->telepon
            ];
        });

        return response()->json($data);
    }

    public function getukuran(Request $request)
    {
        $search = $request->q;

        $ukuran = DB::table('ukuran')
            ->select('id', 'ukuran')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('ukuran', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $ukuran->map(function ($item) {
            return [
                'id' => $item->ukuran,
                'text' => $item->ukuran
            ];
        });

        return response()->json($data);
    }
}
