<?php

namespace App\Http\Controllers\KelolaData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiwayatProdukExport;



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

    public function tambahData(Request $request)
    {
        try {
            $type = $request->type;
            $tableMap = [
                'kategori' => 'kategori',
                'sub_kategori' => 'sub_kategori',
                'penerbit' => 'penerbit',
                'cover' => 'cover',
                'kertas' => 'kertas',
                'kualitas' => 'kualitas',
                'harakat' => 'harakat',
                'penulis' => 'penulis',
                'supplier' => 'supplier',
                'ukuran' => 'ukuran'
            ];

            if (!array_key_exists($type, $tableMap)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe data tidak valid'
                ], 400);
            }

            $table = $tableMap[$type];

            if ($type === 'supplier') {
                $request->validate([
                    'nama_supplier' => 'required|string|max:255',
                    'alamat' => 'nullable|string',
                    'telepon' => 'nullable|string|max:20',
                    'email' => 'nullable|string'
                ]);

                $id = DB::table($table)->insertGetId([
                    'nama_supplier' => $request->nama_supplier,
                    'alamat' => $request->alamat,
                    'telepon' => $request->telepon,
                    'email' => $request->email,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $id,
                        'text' => $request->nama_supplier . ' | ' . $request->telepon
                    ]
                ]);
            } elseif ($type === 'ukuran') {
                $request->validate([
                    'ukuran' => 'required|string|max:100|unique:ukuran,ukuran'
                ]);

                DB::table($table)->insert([
                    'ukuran' => $request->ukuran
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'ukuran' => $request->ukuran
                    ]
                ]);
            } else {
                // Validasi khusus untuk sub_kategori
                if ($type === 'sub_kategori') {
                    $request->validate([
                        'nama_arab' => 'required|string|max:255',
                        'nama_indonesia' => 'required|string|max:255',
                        'kategori_id' => 'required|exists:kategori,id'
                    ]);
                } else {
                    $request->validate([
                        'nama_arab' => 'required|string|max:255',
                        'nama_indonesia' => 'required|string|max:255'
                    ]);
                }

                // Cek duplikasi
                $existing = DB::table($table)
                    ->where('nama_arab', $request->nama_arab)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data sudah ada'
                    ], 400);
                }

                // Data untuk insert
                $data = [
                    'nama_arab' => $request->nama_arab,
                    'nama_indonesia' => $request->nama_indonesia,
                ];

                // Tambahkan kategori_id jika yang ditambah adalah sub_kategori
                if ($type === 'sub_kategori') {
                    $data['id_kategori'] = $request->kategori_id;
                }

                DB::table($table)->insert($data);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'nama_arab' => $request->nama_arab,
                        'nama_indonesia' => $request->nama_indonesia
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function filterProdukQuery($request)
    {
        $query = DB::table('produk')
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.cover',
                'produk.kertas',
                'produk.kualitas',
                'produk.harakat',
                'produk.halaman',
                'produk.berat',
                'produk.ukuran',
                'produk.kategori',
                'produk.sub_kategori',
                'produk.penerbit',
                'produk.penulis',
                DB::raw('COALESCE(supplier.nama_supplier, "Tidak ada supplier") AS supplier'),
                'produk.harga_modal',
                'produk.harga_jual',
                'produk.laba',
                'produk.stok',
                'produk.images',
                'produk.link_youtube',
                'produk.editor',
                'produk.deskripsi',
                'produk.created_at',
                'produk.updated_at'
            ]);

        // Terapkan semua filter
        if ($request->kd_produk) {
            $query->where('produk.kd_produk', 'LIKE', "%{$request->kd_produk}%");
        }
        if ($request->judul) {
            $query->where('produk.judul', 'LIKE', "%{$request->judul}%");
        }
        if ($request->penulis) {
            $query->where('produk.penulis', $request->penulis);
        }
        if ($request->kategori) {
            $query->where('produk.kategori', $request->kategori);
        }
        if ($request->sub_kategori) {
            $query->where('produk.sub_kategori', $request->sub_kategori);
        }
        if ($request->penerbit) {
            $query->where('produk.penerbit', $request->penerbit);
        }
        if ($request->supplier) {
            $query->where('supplier.id', $request->supplier);
        }
        if ($request->stok) {
            $query->where('produk.stok', 'LIKE', "%{$request->stok}%");
        }

        return $query->orderBy('produk.created_at', 'desc')->get();
    }



    public function exportProdukPdf(Request $request)
    {
        $produk = $this->filterProdukQuery($request);

        $pdf = Pdf::loadView('exports.produk-pdf', compact('produk'))
            ->setPaper('a3', 'landscape');

        return $pdf->download('data-produk-' . date('Y-m-d') . '.pdf');
    }



    public function exportProdukExcel(Request $request)
    {
        $produk = $this->filterProdukQuery($request);

        return Excel::download(
            new \App\Exports\ProdukExport($produk),
            'data-produk-' . date('Y-m-d') . '.xlsx'
        );
    }


    public function api_index(Request $request)
    {
        try {
            $query = DB::table('produk')
                ->leftJoin('produk_indo', 'produk_indo.id_produk', '=', 'produk.id')
                ->select([
                    'produk.id',
                    'produk.judul',
                    'produk_indo.judul_indo',
                    'produk.kategori',
                    'produk.sub_kategori',
                    'produk.penulis',
                    'produk.penerbit',
                    'produk.harakat',
                    'produk.cover',
                    'produk.stok',
                    'produk.images',
                    'produk.harga_jual',
                    'produk.harga_modal',
                    'produk.created_at',
                    'produk.link_youtube',
                ]);

            // Tambahkan filter jika ada parameter kategori
            if ($request->has('kategori')) {
                $query->where('produk.kategori', $request->kategori);
            }

            if ($request->has('penulis')) {
                $query->where('produk.penulis', $request->penulis);
            }
            if ($request->has('penerbit')) {
                $query->where('produk.penerbit', $request->penerbit);
            }
            if ($request->has('harakat')) {
                $query->where('produk.harakat', $request->harakat);
            }
            if ($request->has('cover')) {
                $query->where('produk.cover', $request->cover);
            }

            // Tambahkan filter jika ada parameter subkategori
            if ($request->has('subkategori')) {
                $query->where('produk.sub_kategori', $request->subkategori);
            }

            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('produk.judul', 'like', $searchTerm)
                        ->orWhere('produk_indo.judul_indo', 'like', $searchTerm)
                        ->orWhere('produk.penulis', 'like', $searchTerm)
                        ->orWhere('produk.kategori', 'like', $searchTerm);
                });
            }


            $total = $query->count();

            $limit = $request->input('limit', 10); // Default 10 jika tidak diset
            $offset = $request->input('offset', 0); // Default 0 jika tidak diset

            $query->limit($limit)->offset($offset);

            $query->orderBy('produk.created_at', 'desc');

            $produk = $query->get();

            $produk->transform(function ($item) {
                $item->images = $this->processImages($item->images);
                $titleForSlug = $item->judul_indo ?? $item->judul;
                $item->slug = Str::slug($titleForSlug) . '-' . $item->id;
                $item->meta_description = substr(strip_tags($item->judul_indo), 0, 160);
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $produk,
                'total' => $total,
                'current_limit' => $limit,
                'current_offset' => $offset,
                'seo' => [
                    'title' => 'Koleksi Kitab Arab Terlengkap | Al-Kitab',
                    'description' => 'Temukan berbagai macam kitab Arab dari berbagai disiplin ilmu Islam dengan kualitas terbaik dan harga terjangkau.',
                    'keywords' => 'kitab arab, buku islam, fiqh, hadits, tafsir, aqidah, bahasa arab',
                    'canonical' => url('/api/produk')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function api_show($id)
    {
        try {
            $produk = DB::table('produk')
                ->leftJoin('produk_indo', 'produk_indo.id_produk', '=', 'produk.id')
                ->select([
                    'produk.id',
                    'produk.judul',
                    'produk_indo.judul_indo',
                    'produk.kategori',
                    'produk.sub_kategori',
                    'produk.penulis',
                    'produk.stok',
                    'produk.images',
                    'produk.penerbit',
                    'produk.halaman',
                    'produk.cover',
                    'produk.kertas',
                    'produk.berat',
                    'produk.harga_jual', // tambahkan field yang mungkin diperlukan
                    'produk.harga_modal',
                    'produk.link_youtube', // tambahkan field yang mungkin diperlukan
                ])
                ->where('produk.id', $id)
                ->first();

            if (!$produk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $produk->slug = Str::slug($produk->judul_indo ?? $produk->judul) . '-' . $produk->id;

            $produk->images = $this->processImages($produk->images);

            return response()->json([
                'success' => true,
                'message' => 'Detail produk berhasil diambil',
                'data' => $produk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function processImages($imagesJson)
    {
        if (empty($imagesJson)) {
            return [];
        }

        $imagesArray = json_decode($imagesJson, true);

        // If json_decode fails or not an array
        if (!is_array($imagesArray)) {
            return [];
        }

        return array_map(function ($image) {
            return $this->generateImageUrl($image);
        }, $imagesArray);
    }

    /**
     * Generate full URL for product image
     */
    protected function generateImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        // Check if the path is already a URL
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Assuming images are stored in storage/app/public/product
        // and symbolic link is created to public/storage
        return Storage::disk('public')->url('products/' . ltrim($imagePath, '/'));
    }
    public function load(Request $request)
    {
        // dd($request->all());
        $query = DB::table('produk')
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select([
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                DB::raw('COALESCE(supplier.nama_supplier, "Tidak ada supplier") as supplier'),
                'produk.stok',
                'produk.harga_modal',
                'produk.images',
                'produk.harga_jual'
            ]);

        // Filter untuk setiap kolom
        if ($request->has('columns')) {
            foreach ($request->columns as $column) {
                if ($column['search']['value'] != '') {
                    $searchValue = $column['search']['value'];
                    switch ($column['data']) {
                        case 'kd_produk':
                            $query->where('produk.kd_produk', 'like', "%{$searchValue}%");
                            break;
                        case 'judul':
                            $query->where('produk.judul', 'like', "%{$searchValue}%");
                            break;
                        case 'penulis':
                            $query->where('produk.penulis', $searchValue);
                            break;
                        case 'kategori':
                            $query->where('produk.kategori', $searchValue);
                            break;
                        case 'penerbit':
                            $query->where('produk.penerbit', $searchValue);
                            break;
                        case 'supplier':
                            $query->where('supplier.id', $searchValue);
                            break;
                        case 'stok':
                            $query->where('produk.stok', 'like', "%{$searchValue}%");
                            break;
                    }
                }
            }
        }

        $totalsQuery = clone $query;

        // Reset select agar tidak bentrok dengan ONLY_FULL_GROUP_BY
        $totalsQuery->select(DB::raw("
    SUM(produk.stok) as total_stok,
    SUM(produk.harga_jual * produk.stok) as total_harga
"));

        // Hapus order & limit yang dibawa clone dari datatables
        $totalsQuery->orders = null;
        $totalsQuery->limit = null;
        $totalsQuery->offset = null;

        // Ambil total
        $totals = $totalsQuery->first();


        return DataTables::of($query)
            ->with([
                'total_stok' => $totals->total_stok ?? 0,
                'total_harga' => $totals->total_harga ?? 0,
            ])
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $riwayatButton = '<a href="' . route('kelola_data.produk.riwayat', Crypt::encrypt($row->id)) . '"
                        class="btn btn-sm btn-secondary me-1"
                        title="Riwayat Produk">
                        <i class="fas fa-history"></i>
                      </a>';
                $viewButton = '<a href="' . route('kelola_data.produk.show', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-info me-1" title="Lihat Detail"><i class="fas fa-eye"></i></a>';
                $editButton = '<a href="' . route('kelola_data.produk.edit', Crypt::encrypt($row->id)) . '" class="btn btn-sm btn-warning me-1" title="Edit Produk"><i class="fas fa-edit"></i></a>';
                $barcodeButton = '<button class="btn btn-sm btn-primary me-1 barcode-btn" data-kd="' . $row->kd_produk . '" data-judul="' . $row->judul . '"><i class="fas fa-barcode"></i></button>';
                $deleteButton = '<button class="btn btn-sm btn-danger delete-btn" data-url="' . route('kelola_data.produk.destroy', Crypt::encrypt($row->id)) . '"><i class="fas fa-trash-alt"></i></button>';
                return $riwayatButton . $viewButton . $editButton . $barcodeButton . $deleteButton;
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
            $excludedColumns = ['id', 'kd_produk', 'sub_kategori', 'images', 'penulis', 'link_youtube', 'laba', 'created_at', 'updated_at', 'deleted_at'];
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
                'link_youtube',
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
                'laba',
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
        return view('kelola_data.produk.create', [
            'produk' => null,
            'kategori' => [],
            'subKategori' => [],
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'judul_arab' => 'required|string|max:255',
            'judul_indonesia' => 'required|string|max:255',
            'link_youtube' => 'nullable|string|url|max:255',
            'kategori' => 'nullable',
            'kategori_indonesia' => 'nullable',
            'sub_kategori' => 'nullable',
            'sub_kategori_indonesia' => 'nullable',
            'penerbit' => 'nullable',
            'penerbit_indonesia' => 'nullable',
            'cover' => 'nullable',
            'cover_indonesia' => 'nullable',
            'kertas' => 'nullable',
            'kertas_indonesia' => 'nullable',
            'kualitas' => 'nullable',
            'kualitas_indonesia' => 'nullable',
            'harakat' => 'nullable',
            'harakat_indonesia' => 'nullable',
            'penulis' => 'nullable',
            'penulis_indonesia' => 'nullable',
            'supplier' => 'nullable',
            'halaman' => 'nullable|integer',
            'berat' => 'nullable',
            'ukuran' => 'nullable|string',
            'harga_modal' => 'nullable|numeric',
            'harga_jual' => 'nullable|numeric',
            'stok' => 'nullable|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'judul_arab.required' => 'Judul produk (Arab) wajib diisi',
            'judul_indonesia.required' => 'Judul produk (Indonesia) wajib diisi',
            'link_youtube.url' => 'Harus masukan dalam bentuk url',
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
                'kategori' => json_encode($request->kategori, JSON_UNESCAPED_UNICODE),
                'sub_kategori' => json_encode($request->sub_kategori, JSON_UNESCAPED_UNICODE),
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
                'laba' => $request->harga_jual - $request->harga_modal,
                'stok' => $request->stok,
                'images' => !empty($imageNames) ? json_encode($imageNames) : null,
                'link_youtube' => $request->link_youtube,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Tambahkan kolom dinamis ke data produk
            $dynamicColumns = DB::getSchemaBuilder()->getColumnListing('produk');
            foreach ($dynamicColumns as $column) {
                // Jangan timpa kategori & sub_kategori karena sudah di-json_encode
                if (in_array($column, ['kategori', 'sub_kategori', 'kategori_indo', 'sub_kategori_indo', 'images'])) {
                    continue;
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
                'kategori_indo' => json_encode(explode(',', $request->kategori_indonesia), JSON_UNESCAPED_UNICODE),
                'sub_kategori_indo' => json_encode(explode(',', $request->sub_kategori_indonesia), JSON_UNESCAPED_UNICODE),
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
            'link_youtube' => 'nullable|string|url|max:255',
            'kategori' => 'nullable',
            'kategori_indonesia' => 'nullable',
            'sub_kategori' => 'nullable',
            'sub_kategori_indonesia' => 'nullable',
            'penerbit' => 'nullable',
            'penerbit_indonesia' => 'nullable',
            'cover' => 'nullable',
            'cover_indonesia' => 'nullable',
            'kertas' => 'nullable',
            'kertas_indonesia' => 'nullable',
            'kualitas' => 'nullable',
            'kualitas_indonesia' => 'nullable',
            'harakat' => 'nullable',
            'harakat_indonesia' => 'nullable',
            'penulis' => 'nullable',
            'penulis_indonesia' => 'nullable',
            'supplier' => 'nullable',
            'halaman' => 'nullable|integer',
            'berat' => 'nullable',
            'ukuran' => 'nullable|string',
            'harga_modal' => 'nullable|numeric',
            'harga_jual' => 'nullable|numeric',
            'stok' => 'nullable|integer',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images.*' => 'sometimes|string'
        ], [
            'judul_arab.required' => 'Judul produk (Arab) wajib diisi',
            'judul_indonesia.required' => 'Judul produk (Indonesia) wajib diisi',
            'link_youtube.url' => 'Harus masukan dalam bentuk url',
            'sub_kategori.required' => 'Sub Kategori wajib dipilih',
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
                'kategori' => json_encode($request->kategori, JSON_UNESCAPED_UNICODE),
                'sub_kategori' => json_encode($request->sub_kategori, JSON_UNESCAPED_UNICODE),
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
                'laba' => $request->harga_jual - $request->harga_modal,
                'stok' => $request->stok,
                'images' => !empty($imageNames) ? json_encode($imageNames) : null,
                'link_youtube' => $request->link_youtube,
                'updated_at' => now(),
            ];

            // Tambahkan kolom dinamis untuk tabel produk
            $dynamicColumns = DB::getSchemaBuilder()->getColumnListing('produk');
            foreach ($dynamicColumns as $column) {
                if (in_array($column, ['id', 'kd_produk', 'created_at', 'updated_at', 'images', 'kategori', 'sub_kategori'])) {
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
                'kategori_indo' => json_encode(explode(',', $request->kategori_indonesia), JSON_UNESCAPED_UNICODE),
                'sub_kategori_indo' => json_encode(explode(',', $request->sub_kategori_indonesia), JSON_UNESCAPED_UNICODE),
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
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select(
                'produk.*',
                'produk.id as id_produk_asli',
                'produk_indo.*',
                DB::raw('COALESCE(supplier.nama_supplier, "Tidak ada supplier") as nama_supplier'),
                DB::raw('COALESCE(supplier.telepon, "-") as telepon')
            )
            ->where('produk.id', $decryptedId)
            ->first();


        $kategori = [];
        if ($produk->kategori) {
            foreach (json_decode($produk->kategori) as $item) {
                $kategori[] = DB::table('kategori')
                    ->where('nama_arab', $item)
                    ->select(
                        DB::raw("nama_arab AS id"),
                        'nama_arab',
                        'nama_indonesia',
                        DB::raw("CONCAT(nama_arab,' | ',nama_indonesia) AS text")
                    )
                    ->first();
            }
        }

        // Ambil data sub kategori lengkap
        $subKategori = [];
        if ($produk->sub_kategori) {
            foreach (json_decode($produk->sub_kategori) as $item) {
                $subKategori[] = DB::table('sub_kategori')
                    ->where('nama_arab', $item)
                    ->select(
                        DB::raw("nama_arab AS id"),
                        'nama_arab',
                        'nama_indonesia',
                        DB::raw("CONCAT(nama_arab,' | ',nama_indonesia) AS text")
                    )
                    ->first();
            }
        }

        return view('kelola_data.produk.create', compact('produk', 'kategori', 'subKategori'));
    }

    public function riwayat($id)
    {
        $decryptedId = Crypt::decrypt($id);

        $produk = DB::table('produk')
            ->join('produk_indo', 'produk.id', '=', 'produk_indo.id_produk')
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->select(
                'produk.*',
                'produk.id as id_produk_asli',
                'produk_indo.*',
                'supplier.nama_supplier',
                DB::raw('COALESCE(supplier.nama_supplier, "Tidak ada supplier") as nama_supplier'),
                DB::raw('COALESCE(supplier.telepon, "-") as telepon')
            )
            ->where('produk.id', $decryptedId)
            ->first();

        return view('kelola_data.produk.riwayat', compact('produk'));
    }

    public function load_riwayat(Request $request)
    {
        $id_produk = $request->input('id_produk');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $filterType = $request->input('type');
        $filterUser = $request->input('user');

        if (!$id_produk) {
            return DataTables::of([])->make(true);
        }

        $barangMasuk = DB::table('barang_masuk')
            ->leftJoin('users', 'barang_masuk.user_id', '=', 'users.id')
            ->where('barang_masuk.id_produk', $id_produk)
            ->select(
                'barang_masuk.created_at',
                DB::raw('barang_masuk.stok_masuk as qty'),
                DB::raw("'Masuk' as type"),
                'barang_masuk.notes',
                DB::raw('COALESCE(users.name, "System") as user')
            );

        $barangKeluar = DB::table('barang_keluar')
            ->leftJoin('users', 'barang_keluar.user_id', '=', 'users.id')
            ->where('barang_keluar.id_produk', $id_produk)
            ->select(
                'barang_keluar.created_at',
                DB::raw('barang_keluar.stok_keluar as qty'),
                DB::raw("'Keluar' as type"),
                'barang_keluar.notes',
                DB::raw('COALESCE(users.name, "System") as user')
            );

        $transaksi = DB::table('transaksi_items')
            ->leftJoin('transaksi', 'transaksi.id', '=', 'transaksi_items.id_transaksi')
            ->leftJoin('produk', 'produk.kd_produk', '=', 'transaksi_items.kd_produk')
            ->where('produk.id', $id_produk)
            ->select(
                'transaksi_items.created_at',
                'transaksi_items.quantity as qty',
                DB::raw("'Transaksi' as type"),
                DB::raw('"Penjualan" as notes'),
                DB::raw('
                CASE
                    WHEN transaksi.nama_customer IS NOT NULL AND transaksi.nama_customer != ""
                        THEN transaksi.nama_customer
                    WHEN transaksi.nama_pengirim IS NOT NULL AND transaksi.nama_pengirim != ""
                        THEN transaksi.nama_pengirim
                    ELSE "System"
                END as user
            ')
            );

        $riwayatUnion = $barangMasuk->union($barangKeluar)->union($transaksi);

        $riwayat = DB::query()
            ->fromSub($riwayatUnion, 'riwayat')
            ->when($tanggalAwal && $tanggalAkhir, function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween(DB::raw('DATE(riwayat.created_at)'), [$tanggalAwal, $tanggalAkhir]);
            })
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('riwayat.type', $filterType);
            })
            ->when($filterUser, function ($q) use ($filterUser) {
                $q->where('riwayat.user', 'like', "%$filterUser%");
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($riwayat)
            ->addIndexColumn()
            ->addColumn('tanggal', fn($row) => date('d-m-Y', strtotime($row->created_at)))
            ->addColumn('jam', fn($row) => date('H:i:s', strtotime($row->created_at)))
            ->make(true);
    }



    public function exportPdf(Request $request)
    {
        $riwayat = $this->getRiwayatFiltered($request);
        $produk = DB::table('produk')
            ->join('produk_indo', 'produk.id', '=', 'produk_indo.id_produk')
            ->where('produk.id', $request->id_produk)
            ->first();

        $data = [
            'riwayat' => $riwayat,
            'produk' => $produk,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'filter_type' => $request->type,
            'filter_user' => $request->user,
        ];

        $pdf = PDF::loadView('exports.riwayat-pdf', $data);
        return $pdf->download('riwayat-produk-' . date('Y-m-d') . '.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        $riwayat = $this->getRiwayatFiltered($request);
        $produk = DB::table('produk')
            ->join('produk_indo', 'produk.id', '=', 'produk_indo.id_produk')
            ->where('produk.id', $request->id_produk)
            ->first();

        return Excel::download(
            new RiwayatProdukExport($riwayat, $produk, $request->all()),
            'riwayat-produk-' . date('Y-m-d') . '.xlsx'
        );
    }

    private function getRiwayatFiltered(Request $request)
    {
        $id_produk = $request->input('id_produk');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $filterType = $request->input('type');
        $filterUser = $request->input('user');

        $barangMasuk = DB::table('barang_masuk')
            ->leftJoin('users', 'barang_masuk.user_id', '=', 'users.id')
            ->where('barang_masuk.id_produk', $id_produk)
            ->select(
                'barang_masuk.created_at',
                DB::raw('barang_masuk.stok_masuk as qty'),
                DB::raw("'Masuk' as type"),
                'barang_masuk.notes',
                DB::raw('COALESCE(users.name, "System") as user')
            );

        $barangKeluar = DB::table('barang_keluar')
            ->leftJoin('users', 'barang_keluar.user_id', '=', 'users.id')
            ->where('barang_keluar.id_produk', $id_produk)
            ->select(
                'barang_keluar.created_at',
                DB::raw('barang_keluar.stok_keluar as qty'),
                DB::raw("'Keluar' as type"),
                'barang_keluar.notes',
                DB::raw('COALESCE(users.name, "System") as user')
            );

        $transaksi = DB::table('transaksi_items')
            ->leftJoin('transaksi', 'transaksi.id', '=', 'transaksi_items.id_transaksi')
            ->leftJoin('produk', 'produk.kd_produk', '=', 'transaksi_items.kd_produk')
            ->where('produk.id', $id_produk)
            ->select(
                'transaksi_items.created_at',
                'transaksi_items.quantity as qty',
                DB::raw("'Transaksi' as type"),
                DB::raw('"Penjualan" as notes'),
                DB::raw('
                CASE
                    WHEN transaksi.nama_customer IS NOT NULL AND transaksi.nama_customer != ""
                        THEN transaksi.nama_customer
                    WHEN transaksi.nama_pengirim IS NOT NULL AND transaksi.nama_pengirim != ""
                        THEN transaksi.nama_pengirim
                    ELSE "System"
                END as user
            ')
            );

        $riwayatUnion = $barangMasuk->union($barangKeluar)->union($transaksi);

        return DB::query()
            ->fromSub($riwayatUnion, 'riwayat')
            ->when($tanggalAwal && $tanggalAkhir, function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween(DB::raw('DATE(riwayat.created_at)'), [$tanggalAwal, $tanggalAkhir]);
            })
            ->when($filterType, fn($q) => $q->where('riwayat.type', $filterType))
            ->when($filterUser, fn($q) => $q->where('riwayat.user', 'like', "%$filterUser%"))
            ->orderBy('created_at', 'desc')
            ->get();
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


    public function getkategori1(Request $request)
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
                'id' => $item->id,
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
        $kategoriList = (array) $request->kategori;


        $query = DB::table('sub_kategori');

        if (!empty($kategoriList)) {

            $kategoriIDs = DB::table('kategori')
                ->whereIn('nama_arab', $kategoriList)
                ->pluck('id');

            if ($kategoriIDs->count() > 0) {
                $query->whereIn('id_kategori', $kategoriIDs);
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
