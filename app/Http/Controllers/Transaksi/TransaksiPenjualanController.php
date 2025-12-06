<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class TransaksiPenjualanController extends Controller
{
    public function index()
    {
        return view('transaksi.transaksi_penjualan.index');
    }

    public function load(Request $request)
    {
        $query = DB::table('produk')
            ->leftJoin('supplier', 'produk.supplier', '=', 'supplier.id')
            ->where('produk.stok', '>', 0)
            ->select([
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.penulis',
                'produk.kategori',
                'produk.penerbit',
                'supplier.nama_supplier as supplier',
                'produk.stok',
                'produk.harga_jual',
                'produk.harga_modal'
            ]);


        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('kategori', function ($row) {
                // Convert JSON array → string
                if ($row->kategori) {
                    $kategoriArray = json_decode($row->kategori, true);
                    if (is_array($kategoriArray)) {
                        return implode(', ', $kategoriArray);
                    }
                }
                return '-';
            })
            ->addColumn('aksi', function ($row) {
                return '';
            })
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('produk.kd_produk', 'like', "%{$search}%")
                            ->orWhere('produk.judul', 'like', "%{$search}%")
                            ->orWhere('produk.penulis', 'like', "%{$search}%")
                            ->orWhere('produk.kategori', 'like', "%{$search}%")
                            ->orWhere('produk.penerbit', 'like', "%{$search}%")
                            ->orWhere('supplier.nama_supplier', 'like', "%{$search}%");
                    });
                }
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function simpanDraft(Request $request)
    {

        DB::beginTransaction();
        try {
            // VALIDASI SANGAT RINGAN UNTUK DRAFT
            $request->validate([
                'customer' => 'nullable|exists:customer,id',
                'items' => 'required|array|min:1',
                'items.*.kd_produk' => 'required|exists:produk,kd_produk',
                'items.*.quantity' => 'required|integer|min:0',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.diskon_produk' => 'nullable|numeric|min:0|max:100',
            ]);

            // CEK APAKAH ADA DRAFT_ID UNTUK UPDATE
            $draftId = $request->draft_id;
            $isUpdate = !empty($draftId);

            if ($isUpdate) {
                // UPDATE DRAFT YANG SUDAH ADA
                $draft = DB::table('transaksi')
                    ->where('id', $draftId)
                    ->where('status_transaksi', 'draft')
                    ->where('kasir', auth()->user()->id)
                    ->first();

                if (!$draft) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Draft tidak ditemukan untuk diupdate'
                    ], 404);
                }

                $kodeTransaksi = $draft->kode_transaksi;

                // Hapus items lama
                DB::table('transaksi_items')
                    ->where('id_transaksi', $draftId)
                    ->delete();
            } else {
                // BUAT DRAFT BARU
                $lastTransaksi = DB::table('transaksi')
                    ->whereDate('created_at', today())
                    ->orderBy('nomor_urut', 'desc')
                    ->first();

                $kodeTransaksi = 'DRAFT-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Get customer data
            $customer = $request->customer ? DB::table('customer')->where('id', $request->customer)->first() : null;

            $totalItems = 0;
            foreach ($request->items as $item) {
                $diskonProduk = $item['diskon_produk'] ?? 0;
                $hargaSetelahDiskonProduk = $item['unit_price'];

                if ($diskonProduk > 0 && $diskonProduk <= 100) {
                    $hargaSetelahDiskonProduk = $item['unit_price'] * (1 - ($diskonProduk / 100));
                }

                $totalItems += $hargaSetelahDiskonProduk * $item['quantity'];
            }

            // Hitung total akhir dengan potongan, diskon, ongkir, dll
            $subtotal = $request->subtotal ?? $totalItems;
            $potongan = $request->potongan ?? 0;
            $diskonPersen = $request->diskon_persen ?? 0;
            $ongkir = $request->ongkir ?? 0;
            $packingKayu = $request->packing_kayu ?? 0;

            // Hitung diskon nominal dari persentase
            $diskonNominal = ($subtotal - $potongan) * ($diskonPersen / 100);

            // Total akhir
            $totalAkhir = $subtotal - $potongan - $diskonNominal + $ongkir + $packingKayu;

            // Untuk draft, TIDAK mengurangi stok dan deposit
            $usedDeposit = 0;
            $remainingAmount = $totalAkhir;

            $transaksiData = [
                'kode_transaksi' => $kodeTransaksi,
                'id_customer' => $request->customer,
                'ekspedisi' => $request->ekspedisi,
                'nama_customer' => $customer ? $customer->nama : null,
                'no_hp_customer' => $customer ? $customer->no_hp : null,
                'alamat_customer' => $customer ? $customer->alamat : null,
                'payment_method' => $request->payment_method ?? 'tunai',
                'payment_status' => $request->payment_status ?? 'lunas',
                'channel_order' => $request->channel_order ?? 'Offline',
                'subtotal' => $request->subtotal ?? 0,
                'discount' => $request->diskon_persen ?? 0,
                'potongan' => $request->potongan ?? 0,
                'total' => $request->total ?? 0,
                'paid_amount' => $request->paid_amount ?? 0,
                'remaining_amount' => $remainingAmount,
                'change_amount' => 0,
                'notes' => $request->notes,
                'updated_at' => now(),
                'kasir' => auth()->user()->id,
                'is_dropship' => $request->is_dropship ?? 0,
                'nama_pengirim' => $request->nama_pengirim,
                'telepon_pengirim' => $request->telepon_pengirim,
                'alamat_pengirim' => $request->alamat_pengirim,
                'nama_penerima' => $request->nama_penerima,
                'telepon_penerima' => $request->telepon_penerima,
                'alamat_penerima' => $request->alamat_penerima,
                'ongkir' => $request->ongkir ?? 0,
                'packing_kayu' => $request->packing_kayu ?? 0,
                'status_transaksi' => 'draft',
            ];

            if ($isUpdate) {
                // UPDATE DRAFT YANG SUDAH ADA
                DB::table('transaksi')
                    ->where('id', $draftId)
                    ->update($transaksiData);

                $transaksiId = $draftId;
            } else {
                // BUAT DRAFT BARU
                $transaksiData['created_at'] = now();
                $transaksiId = DB::table('transaksi')->insertGetId($transaksiData);
            }

            // Simpan item transaksi DRAFT
            foreach ($request->items as $item) {
                $diskonProduk = $item['diskon_produk'] ?? 0;
                $hargaSetelahDiskonProduk = $item['unit_price'];

                if ($diskonProduk > 0 && $diskonProduk <= 100) {
                    $hargaSetelahDiskonProduk = $item['unit_price'] * (1 - ($diskonProduk / 100));
                }

                // Hitung total harga setelah diskon produk
                $totalHargaSetelahDiskon = $hargaSetelahDiskonProduk * $item['quantity'];
                DB::table('transaksi_items')->insert([
                    'id_transaksi' => $transaksiId,
                    'kd_produk' => $item['kd_produk'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'original_price' => $item['original_price'],
                    'diskon_produk' => $diskonProduk,
                    'is_custom_price' => $item['unit_price'] != $item['original_price'],
                    'total_price' => $totalHargaSetelahDiskon,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isUpdate ? 'Draft berhasil diupdate' : 'Draft transaksi berhasil disimpan',
                'data' => [
                    'kode_transaksi' => $kodeTransaksi,
                    'transaksi_id' => $transaksiId,
                    'status' => 'draft',
                    'is_update' => $isUpdate
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan draft transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getDraftList()
    {
        try {
            $drafts = DB::table('transaksi as t')
                ->leftJoin('customer as c', 't.id_customer', '=', 'c.id')
                ->leftJoin('users as u', 't.kasir', '=', 'u.id')
                ->where('t.status_transaksi', 'draft')
                ->where('t.kasir', auth()->user()->id) // Hanya draft milik kasir yang login
                ->select(
                    't.id',
                    't.kode_transaksi',
                    't.nama_customer',
                    't.total',
                    't.created_at',
                    'u.name as kasir_name',
                    'c.deposit'
                )
                ->orderBy('t.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $drafts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDraftData(Request $request)
    {
        try {
            $draftId = $request->id;

            $draft = DB::table('transaksi')
                ->where('id', $draftId)
                ->where('status_transaksi', 'draft')
                ->where('kasir', auth()->user()->id)
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft tidak ditemukan'
                ], 404);
            }

            // Get items draft - PERBAIKI QUERY INI
            $items = DB::table('transaksi_items as ti')
                ->join('produk as p', 'ti.kd_produk', '=', 'p.kd_produk')
                ->where('ti.id_transaksi', $draftId)
                ->select(
                    'ti.id',
                    'ti.kd_produk',
                    'ti.quantity',
                    'ti.unit_price',
                    'ti.original_price',
                    'ti.total_price',
                    'ti.is_custom_price',
                    'ti.diskon_produk',
                    'p.id as produk_id',
                    'p.judul',
                    'p.penulis',
                    'p.penerbit',
                    'p.supplier',
                    'p.stok',
                    'p.harga_jual',
                    'p.harga_modal'
                )
                ->get();

            // Format items dengan struktur yang diharapkan
            $formattedItems = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kd_produk' => $item->kd_produk,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'original_price' => $item->original_price,
                    'total_price' => $item->total_price,
                    'diskon_produk' => $item->diskon_produk,
                    'is_custom_price' => $item->is_custom_price,
                    'produk' => [
                        'id' => $item->produk_id,
                        'judul' => $item->judul,
                        'penulis' => $item->penulis,
                        'penerbit' => $item->penerbit,
                        'supplier' => $item->supplier,
                        'stok' => $item->stok,
                        'harga_jual' => $item->harga_jual,
                        'harga_modal' => $item->harga_modal
                    ]
                ];
            });

            // Get customer data jika ada
            $customer = null;
            if ($draft->id_customer) {
                $customer = DB::table('customer')
                    ->where('id', $draft->id_customer)
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaksi' => $draft,
                    'items' => $formattedItems,
                    'customer' => $customer
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDraft(Request $request)
    {
        DB::beginTransaction();
        try {
            $draftId = $request->id;

            $draft = DB::table('transaksi')
                ->where('id', $draftId)
                ->where('status_transaksi', 'draft')
                ->where('kasir', auth()->user()->id)
                ->first();

            if (!$draft) {
                return response()->json([
                    'success' => false,
                    'message' => 'Draft tidak ditemukan'
                ], 404);
            }

            // Hapus items terlebih dahulu
            DB::table('transaksi_items')
                ->where('id_transaksi', $draftId)
                ->delete();

            // Hapus transaksi draft
            DB::table('transaksi')
                ->where('id', $draftId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus draft: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cetakInvoice($id)
    {
        $transaksi = DB::table('transaksi')
            ->leftJoin('ekspedisi', 'transaksi.ekspedisi', '=', 'ekspedisi.nama_ekspedisi')
            ->where('transaksi.id', $id)
            ->select('transaksi.*', 'ekspedisi.nama_ekspedisi', 'ekspedisi.ekspedisi_logo')
            ->first();

        if (!$transaksi) abort(404);

        // Kunci per baris item & urutkan — cegah duplikat karena JOIN
        $items = DB::table('transaksi_items')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('transaksi_items.id_transaksi', $id)
            ->select([
                'transaksi_items.id',
                'transaksi_items.kd_produk',
                'transaksi_items.quantity',
                'transaksi_items.unit_price',
                'transaksi_items.original_price',
                'transaksi_items.is_custom_price',
                'transaksi_items.total_price',
                'produk.judul',
                'produk.penulis',
                'produk.penerbit',
            ])
            ->orderBy('transaksi_items.id')          // urut stabil
            ->groupBy(                                // kunci baris unik
                'transaksi_items.id',
                'transaksi_items.kd_produk',
                'transaksi_items.quantity',
                'transaksi_items.unit_price',
                'transaksi_items.original_price',
                'transaksi_items.is_custom_price',
                'transaksi_items.total_price',
                'produk.judul',
                'produk.penulis',
                'produk.penerbit'
            )
            ->get();

        $profilPerusahaan = DB::table('profile_perusahaan')->first();

        return view('transaksi.transaksi_penjualan.invoice', compact('transaksi', 'items', 'profilPerusahaan'));
    }

    public function cetakInvoiceThermal($id)
    {
        $transaksi = DB::table('transaksi')
            ->leftJoin('ekspedisi', 'transaksi.ekspedisi', '=', 'ekspedisi.nama_ekspedisi')
            ->where('transaksi.id', $id)
            ->select('transaksi.*', 'ekspedisi.nama_ekspedisi', 'ekspedisi.ekspedisi_logo')
            ->first();

        if (!$transaksi) abort(404);

        $items = DB::table('transaksi_items')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('transaksi_items.id_transaksi', $id)
            ->select([
                'transaksi_items.id',
                'transaksi_items.kd_produk',
                'transaksi_items.quantity',
                'transaksi_items.unit_price',
                'transaksi_items.total_price',
                'produk.judul',
            ])
            ->orderBy('transaksi_items.id')
            ->groupBy(
                'transaksi_items.id',
                'transaksi_items.kd_produk',
                'transaksi_items.quantity',
                'transaksi_items.unit_price',
                'transaksi_items.total_price',
                'produk.judul'
            )
            ->get();

        $profilPerusahaan = DB::table('profile_perusahaan')->first();

        // Hitung komponen untuk struk
        $subtotal   = $transaksi->subtotal ?? $items->sum('total_price');
        $diskon     = (float) ($transaksi->discount ?? 0);
        $potongan   = (float) ($transaksi->potongan ?? 0);
        $ongkir     = (float) ($transaksi->ongkir ?? 0);
        $packing    = (float) ($transaksi->packing_kayu ?? 0);
        $grandTotal = (float) ($transaksi->total ?? ($subtotal - $diskon - $potongan + $ongkir + $packing));
        $dibayar    = (float) ($transaksi->paid_amount ?? 0);
        $kembali    = (float) ($transaksi->change_amount ?? max(0, $dibayar - $grandTotal));
        $sisa       = (float) ($transaksi->remaining_amount ?? max(0, $grandTotal - $dibayar));

        return view('transaksi.transaksi_penjualan.invoice_thermal', compact(
            'transaksi',
            'items',
            'profilPerusahaan',
            'subtotal',
            'diskon',
            'potongan',
            'ongkir',
            'packing',
            'grandTotal',
            'dibayar',
            'kembali',
            'sisa'
        ));
    }


    public function simpanTransaksi(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi input dasar
            $request->validate([
                'channel_order' => 'required',
                'customer' => 'nullable|exists:customer,id',
                'payment_method' => 'required|in:tunai,transfer,qris,dp,cod',
                'payment_status' => 'required|in:lunas,hutang',
                'items' => 'required|array|min:1',
                'items.*.kd_produk' => 'required|exists:produk,kd_produk',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.diskon_produk' => 'nullable|numeric|min:0|max:100',
                'subtotal' => 'required|numeric|min:0',
                'diskon_persen' => 'numeric|min:0',
                'total' => 'required|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'used_deposit' => 'numeric|min:0',
                'ekspedisi' => 'nullable|string|max:255',
                'ekspedisi_lain' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'potongan' => 'numeric|min:0',
                'is_dropship' => 'sometimes|boolean',
                'nama_pengirim' => 'nullable|string|max:255',
                'telepon_pengirim' => 'nullable|string|max:20',
                'alamat_pengirim' => 'nullable|string',
                'nama_penerima' => 'nullable|string|max:255',
                'telepon_penerima' => 'nullable|string|max:20',
                'alamat_penerima' => 'nullable|string',
                'ongkir' => 'numeric|min:0',
                'packing_kayu' => 'numeric|min:0',
                'draft_id' => 'nullable|exists:transaksi,id',
            ]);

            $isUpdateDraft = !empty($request->draft_id);
            $draftId = $request->draft_id;

            if ($isUpdateDraft) {
                // VALIDASI DRAFT MILIK USER YANG SAMA
                $existingDraft = DB::table('transaksi')
                    ->where('id', $draftId)
                    ->where('status_transaksi', 'draft')
                    ->where('kasir', auth()->user()->id)
                    ->first();

                if (!$existingDraft) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Draft tidak ditemukan atau tidak dapat diakses'
                    ], 404);
                }
            }
            $namaEkspedisi = $request->ekspedisi;
            $ekspedisiLogoPath = null;

            // Jika memilih "Lainnya", proses ekspedisi baru
            if ($request->ekspedisi === 'Lainnya') {
                // Validasi khusus untuk ekspedisi lainnya
                $request->validate([
                    'ekspedisi_lain' => 'required|string|max:255',
                    'ekspedisi_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
                ]);

                $namaEkspedisi = $request->ekspedisi_lain;

                // Cek apakah ekspedisi dengan nama yang sama sudah ada
                $existingEkspedisi = DB::table('ekspedisi')
                    ->where('nama_ekspedisi', $namaEkspedisi)
                    ->first();

                if (!$existingEkspedisi) {
                    // Upload logo jika ada
                    if ($request->hasFile('ekspedisi_logo')) {
                        $logo = $request->file('ekspedisi_logo');
                        $fileName = time() . '_' . $logo->getClientOriginalName();
                        $ekspedisiLogoPath = $logo->storeAs('ekspedisi_logos', $fileName, 'public');
                    }

                    // Simpan ekspedisi baru ke database
                    DB::table('ekspedisi')->insert([
                        'nama_ekspedisi' => $namaEkspedisi,
                        'ekspedisi_logo' => $ekspedisiLogoPath,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Generate data transaksi
            $lastTransaksi = DB::table('transaksi')
                ->whereDate('created_at', today())
                ->orderBy('nomor_urut', 'desc')
                ->first();

            $nomorUrut = $lastTransaksi ? $lastTransaksi->nomor_urut + 1 : 1;
            if ($isUpdateDraft) {
                $kodeTransaksi = $existingDraft->kode_transaksi;
                if (strpos($kodeTransaksi, 'DRAFT-') === 0) {
                    $kodeTransaksi = str_replace('DRAFT-', 'TRX-', $kodeTransaksi);
                }
            } else {
                $kodeTransaksi = 'TRX-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Get customer data
            $customer = DB::table('customer')->where('id', $request->customer)->first();
            $depositTersedia = $customer ? $customer->deposit : 0;

            $requestedDeposit = $request->used_deposit ?? 0;
            $totalTransaksi = $request->total;

            // Deposit yang benar-benar digunakan
            $usedDeposit = min($requestedDeposit, $totalTransaksi, $depositTersedia);
            $sisaDeposit = max(0, $requestedDeposit - $totalTransaksi);
            $remainingAmount = max(0, $totalTransaksi - $usedDeposit - $request->paid_amount);
            $totalLaba = 0;
            $totalOmset = 0;


            $transaksiData = [
                'kode_transaksi' => $kodeTransaksi,
                'nomor_urut' => $nomorUrut,
                'id_customer' => $request->customer,
                'ekspedisi' => $namaEkspedisi, // Menggunakan nama ekspedisi yang sudah diproses
                'nama_customer' => $customer ? $customer->nama : null,
                'no_hp_customer' => $customer ? $customer->no_hp : null,
                'alamat_customer' => $customer ? $customer->alamat : null,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'channel_order' => $request->channel_order,
                'subtotal' => $request->subtotal,
                'discount' => $request->diskon_persen ?? 0,
                'potongan' => $request->potongan ?? 0,
                'total' => $request->total,
                'paid_amount' => $request->paid_amount,
                'remaining_amount' => $remainingAmount,
                'change_amount' => max(0, ($request->paid_amount + $usedDeposit) - $totalTransaksi),
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now(),
                'kasir' => auth()->user()->id,
                'is_dropship' => $request->is_dropship ?? 0,
                'nama_pengirim' => $request->nama_pengirim,
                'telepon_pengirim' => $request->telepon_pengirim,
                'alamat_pengirim' => $request->alamat_pengirim,
                'nama_penerima' => $request->nama_penerima,
                'telepon_penerima' => $request->telepon_penerima,
                'alamat_penerima' => $request->alamat_penerima,
                'ongkir' => $request->ongkir ?? 0,
                'packing_kayu' => $request->packing_kayu ?? 0,
                'status_transaksi' => 'completed',
                'totallaba' => 0,
                'totalomset' => 0,
            ];
            if ($isUpdateDraft) {
                // UPDATE DRAFT MENJADI TRANSAKSI COMPLETED
                DB::table('transaksi')
                    ->where('id', $draftId)
                    ->update($transaksiData);

                $transaksiId = $draftId;

                // Hapus items lama sebelum menyimpan yang baru
                DB::table('transaksi_items')
                    ->where('id_transaksi', $draftId)
                    ->delete();
            } else {
                // BUAT TRANSAKSI BARU
                $transaksiData['created_at'] = now();
                $transaksiId = DB::table('transaksi')->insertGetId($transaksiData);
            }

            // Simpan item transaksi
            foreach ($request->items as $item) {
                $hargaSetelahDiskonProduk = $item['unit_price'];

                // Jika ada diskon produk, hitung harga setelah diskon
                $diskonProduk = $item['diskon_produk'] ?? 0;
                if ($diskonProduk > 0 && $diskonProduk <= 100) {
                    $hargaSetelahDiskonProduk = $item['unit_price'] * (1 - ($diskonProduk / 100));
                }

                // Hitung total harga setelah diskon produk
                $totalHargaSetelahDiskon = $hargaSetelahDiskonProduk * $item['quantity'];

                $produk = DB::table('produk')->where('kd_produk', $item['kd_produk'])->first();

                $harga_jual = $produk->harga_jual;
                $harga_modal = $produk->harga_modal;

                // Hitung laba
                $laba = ($harga_jual - $harga_modal) * $item['quantity'];
                $totalOmset += ($harga_jual * $item['quantity']);


                // Tambahkan ke total laba transaksi
                $totalLaba += $laba;
                DB::table('transaksi_items')->insert([
                    'id_transaksi' => $transaksiId,
                    'kd_produk' => $item['kd_produk'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'original_price' => $item['original_price'],
                    'diskon_produk' => $diskonProduk,
                    'harga_setelah_diskon_produk' => $hargaSetelahDiskonProduk,
                    'is_custom_price' => $item['unit_price'] != $item['original_price'],
                    'total_price' => $totalHargaSetelahDiskon,
                    'harga_jual' => $harga_jual,
                    'harga_modal' => $harga_modal,
                    'laba' => $laba,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update stok produk

                DB::table('produk')
                    ->where('kd_produk', $item['kd_produk'])
                    ->decrement('stok', $item['quantity']);
            }

            // Update deposit customer jika diperlukan
            if ($requestedDeposit > 0 && $customer) {
                $totalDepositChange = $usedDeposit - $sisaDeposit;
                if ($totalDepositChange > 0) {
                    DB::table('customer')
                        ->where('id', $request->customer)
                        ->decrement('deposit', $totalDepositChange);
                }
            }

            DB::table('transaksi')
                ->where('id', $transaksiId)
                ->update([
                    'totallaba' => $totalLaba,
                    'totalomset' => $totalOmset,
                    'updated_at' => now()
                ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'kode_transaksi' => $kodeTransaksi,
                    'transaksi_id' => $transaksiId
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getcustomer(Request $request)
    {
        $search = $request->q;

        $customer = DB::table('customer')
            ->select('id', 'nama', 'no_hp', 'deposit')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('no_hp', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $data = $customer->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->nama . ' | ' . $item->no_hp,
                'deposit' => $item->deposit
            ];
        });

        return response()->json($data);
    }

    public function simpanCustomer(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20|unique:customer,no_hp',
            'alamat' => 'required|string'
        ], [
            // Pesan validasi untuk field nama
            'nama.required' => 'Nama customer harus diisi.',
            'nama.string' => 'Nama customer harus berupa teks.',
            'nama.max' => 'Nama customer maksimal 255 karakter.',

            // Pesan validasi untuk field no_hp
            'no_hp.required' => 'Nomor HP harus diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',

            // Pesan validasi untuk field alamat
            'alamat.required' => 'Alamat harus diisi.',
            'alamat.string' => 'Alamat harus berupa teks.'
        ]);

        try {
            $customerId = DB::table('customer')->insertGetId([
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil disimpan',
                'data' => [
                    'id' => $customerId,
                    'nama' => $request->nama,
                    'no_hp' => $request->no_hp,
                    'alamat' => $request->alamat
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEkspedisi()
    {
        $ekspedisi = DB::table('ekspedisi')->select('id', 'nama_ekspedisi')->get();
        return response()->json($ekspedisi);
    }
}
