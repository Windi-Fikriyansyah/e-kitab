<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;



class Dashboard extends Controller
{
    private function parseKategori($kategori)
    {
        $decoded = json_decode($kategori, true);

        if (is_array($decoded)) {
            return implode(', ', $decoded);
        }

        return $kategori;
    }


    public function index(Request $request)
    {
        // Mengambil tipe user yang sedang login
        $user = Auth::user();

        // Mengecek tipe user dan mengarahkan sesuai
        if ($user->tipe === 'owner') {
            return redirect()->route('dashboard-owner'); // Arahkan ke halaman dashboard untuk tipe owner
        } elseif ($user->tipe === 'kasir') {
            return redirect()->route('home'); // Arahkan ke halaman home untuk tipe kasir
        }

        // Jika tidak sesuai tipe, bisa redirect ke halaman default atau login
        return redirect()->route('login');
    }


    public function dashboard(Request $request)
    {
        $dailyIncome = DB::table('transaksi')
            ->where('payment_status', 'lunas')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total');


        // Omset per bulan (bulan ini)
        $monthlyIncome = DB::table('transaksi')
            ->where('payment_status', 'lunas')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $dailyPengeluaran = DB::table('pengeluaran')
            ->whereDate('tanggal', today())
            ->sum('nominal');

        $dailyModal = DB::table('transaksi_items')
            ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('transaksi.payment_status', 'lunas')
            ->whereDate('transaksi.created_at', today())
            ->sum(DB::raw('transaksi_items.unit_price * transaksi_items.quantity'));
        $monthlyModal = DB::table('transaksi_items')
            ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
            ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
            ->where('transaksi.payment_status', 'lunas')
            ->whereMonth('transaksi.created_at', now()->month)
            ->whereYear('transaksi.created_at', now()->year)
            ->sum(DB::raw('transaksi_items.unit_price * transaksi_items.quantity'));

        // Pengeluaran bulanan
        $monthlyPengeluaran = DB::table('pengeluaran')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('nominal');
        $labaPerHari = $dailyIncome - $dailyModal - $dailyPengeluaran;
        $labaPerBulan = $monthlyIncome - $monthlyModal - $monthlyPengeluaran;
        // Total transaksi
        $totalTransactions = DB::table('transaksi')->count();

        // Total customer
        $totalCustomers = DB::table('customer')->count();

        $totalpiutang = DB::table('transaksi')
            ->where('payment_status', 'hutang')
            ->sum('total');

        // 5 Produk terlaris
        $bestSellers = DB::table('produk')
            ->join('transaksi_items', 'produk.kd_produk', '=', 'transaksi_items.kd_produk')
            ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
            ->where('transaksi.payment_status', 'lunas')
            ->select(
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.harga_jual',
                'produk.kategori',
                'produk.stok',
                DB::raw('SUM(transaksi_items.quantity) as total_sold')
            )
            ->groupBy(
                'produk.id',
                'produk.kd_produk',
                'produk.judul',
                'produk.harga_jual',
                'produk.kategori',
                'produk.stok'
            )
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                // Decode JSON kategori
                $kategori = json_decode($item->kategori, true);

                // Jika kategori adalah array → gabungkan dengan koma
                if (is_array($kategori)) {
                    $item->kategori = implode(', ', $kategori);
                }

                return $item;
            });

        $lowStockItems = DB::table('produk')
            ->where('stok', '<', 10)
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        $stokRealTime = DB::table('produk')
            ->select('judul', 'kategori', 'stok', 'harga_jual')
            ->orderBy('judul')
            ->paginate(10);

        // Riwayat Masuk & Keluar
        $riwayatMasuk = DB::table('barang_masuk')
            ->join('produk', 'barang_masuk.id_produk', '=', 'produk.id')
            ->select('barang_masuk.created_at', 'produk.judul', 'barang_masuk.stok_masuk', DB::raw('NULL as stok_keluar'), 'barang_masuk.notes');

        $riwayatKeluar = DB::table('barang_keluar')
            ->join('produk', 'barang_keluar.id_produk', '=', 'produk.id')
            ->select('barang_keluar.created_at', 'produk.judul', DB::raw('NULL as stok_masuk'), 'barang_keluar.stok_keluar', 'barang_keluar.notes');



        return view('dashboard', [
            'dailyIncome' => $dailyIncome,
            'monthlyIncome' => $monthlyIncome,
            'totalTransactions' => $totalTransactions,
            'totalCustomers' => $totalCustomers,
            'totalpiutang' => $totalpiutang,
            'bestSellers' => $bestSellers,
            'lowStockItems' => $lowStockItems,
            'labaPerHari' => $labaPerHari,
            'labaPerBulan' => $labaPerBulan,

        ]);
    }

    public function loadRealtime(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->select('id', 'judul', 'kategori', 'stok', 'harga_jual')
                ->orderBy('judul', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kategori', function ($row) {
                    return $this->parseKategori($row->kategori);
                })
                ->editColumn('harga_jual', fn($row) => 'Rp ' . number_format($row->harga_jual, 0, ',', '.'))
                ->make(true);
        }
    }

    public function loadRiwayat(Request $request)
    {
        if ($request->ajax()) {
            $riwayatMasuk = DB::table('barang_masuk')
                ->join('produk', 'barang_masuk.id_produk', '=', 'produk.id')
                ->select('barang_masuk.created_at as tanggal', 'produk.judul', 'barang_masuk.stok_masuk', DB::raw('NULL as stok_keluar'), 'barang_masuk.notes');

            $riwayatKeluar = DB::table('barang_keluar')
                ->join('produk', 'barang_keluar.id_produk', '=', 'produk.id')
                ->select('barang_keluar.created_at as tanggal', 'produk.judul', DB::raw('NULL as stok_masuk'), 'barang_keluar.stok_keluar', 'barang_keluar.notes');

            $riwayat = $riwayatMasuk->unionAll($riwayatKeluar);

            return DataTables::of($riwayat)
                ->addIndexColumn()
                ->editColumn('tanggal', fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y'))
                ->make(true);
        }
    }

    public function loadAging(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->select('judul', 'kategori', 'stok', DB::raw('DATEDIFF(NOW(), updated_at) as usia_stok'))
                ->orderByDesc('usia_stok')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kategori', function ($row) {
                    return $this->parseKategori($row->kategori);
                })
                ->make(true);
        }
    }

    public function loadPergerakan(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->leftJoinSub(
                    DB::table('barang_masuk')
                        ->select('id_produk', DB::raw('SUM(stok_masuk) as total_masuk'))
                        ->whereMonth('created_at', now()->month)
                        ->groupBy('id_produk'),
                    'masuk',
                    'produk.id',
                    '=',
                    'masuk.id_produk'
                )
                ->leftJoinSub(
                    DB::table('barang_keluar')
                        ->select('id_produk', DB::raw('SUM(stok_keluar) as total_keluar'))
                        ->whereMonth('created_at', now()->month)
                        ->groupBy('id_produk'),
                    'keluar',
                    'produk.id',
                    '=',
                    'keluar.id_produk'
                )
                ->select(
                    'produk.judul',
                    DB::raw('COALESCE(masuk.total_masuk, 0) as total_masuk'),
                    DB::raw('COALESCE(keluar.total_keluar, 0) as total_keluar'),
                    'produk.stok'
                )
                ->get();

            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    public function loadLaporanSupplier(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->select(
                    'supplier.nama_supplier as supplier',
                    'produk.kd_produk',
                    'produk.judul',
                    'produk.kategori',
                    'produk.harga_modal',
                    'produk.harga_jual',
                    'produk.stok'
                )
                ->orderBy('supplier.nama_supplier')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('kategori', function ($row) {
                    return $this->parseKategori($row->kategori);
                })
                ->editColumn('harga_modal', fn($row) => 'Rp ' . number_format($row->harga_modal, 0, ',', '.'))
                ->editColumn('harga_jual', fn($row) => 'Rp ' . number_format($row->harga_jual, 0, ',', '.'))
                ->make(true);
        }
    }

    public function loadTagihanSupplier(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->select(
                    'supplier.nama_supplier',
                    DB::raw('COUNT(produk.id) as jumlah_produk'),
                    DB::raw('SUM(produk.harga_modal * produk.stok) as total_tagihan'),
                    DB::raw('MAX(produk.updated_at) as tanggal_terakhir')
                )
                ->groupBy('supplier.nama_supplier')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('total_tagihan', fn($row) => 'Rp ' . number_format($row->total_tagihan, 0, ',', '.'))
                ->editColumn('tanggal_terakhir', fn($row) => \Carbon\Carbon::parse($row->tanggal_terakhir)->format('d/m/Y'))
                ->make(true);
        }
    }

    public function loadRingkasanSupplier(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('produk')
                ->join('supplier', 'produk.supplier', '=', 'supplier.id')
                ->select(
                    'supplier.nama_supplier',
                    DB::raw('COUNT(produk.id) as total_produk'),
                    DB::raw('SUM(produk.stok) as total_stok'),
                    DB::raw('SUM(produk.harga_modal * produk.stok) as total_modal'),
                    DB::raw('SUM(produk.harga_jual * produk.stok) as total_jual')
                )
                ->groupBy('supplier.nama_supplier')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('total_modal', fn($r) => 'Rp ' . number_format($r->total_modal, 0, ',', '.'))
                ->editColumn('total_jual', fn($r) => 'Rp ' . number_format($r->total_jual, 0, ',', '.'))
                ->make(true);
        }
    }
    public function loadLabaRugi(Request $request)
    {
        if ($request->ajax()) {

            // Ambil omzet per tanggal dari transaksi
            $omzetData = DB::table('transaksi')
                ->where('payment_status', 'lunas')
                ->select(
                    DB::raw('DATE(created_at) as tanggal'),
                    DB::raw('SUM(total) as omzet')
                )
                ->groupBy(DB::raw('DATE(created_at)'));

            // Hitung total modal berdasarkan produk × quantity
            $modalData = DB::table('transaksi_items')
                ->join('transaksi', 'transaksi_items.id_transaksi', '=', 'transaksi.id')
                ->join('produk', 'transaksi_items.kd_produk', '=', 'produk.kd_produk')
                ->where('transaksi.payment_status', 'lunas')
                ->select(
                    DB::raw('DATE(transaksi.created_at) as tanggal'),
                    DB::raw('SUM(produk.harga_modal * transaksi_items.quantity) as modal')
                )
                ->groupBy(DB::raw('DATE(transaksi.created_at)'));
            $pengeluaranData = DB::table('pengeluaran')
                ->select(
                    DB::raw('DATE(tanggal) as tanggal'),
                    DB::raw('SUM(nominal) as pengeluaran')
                )
                ->groupBy(DB::raw('DATE(tanggal)'));
            // Gabungkan omzet + modal
            $data = DB::table(DB::raw("({$omzetData->toSql()}) as o"))
                ->mergeBindings($omzetData)
                ->leftJoinSub($modalData, 'm', 'o.tanggal', '=', 'm.tanggal')
                ->leftJoinSub($pengeluaranData, 'p', 'o.tanggal', '=', 'p.tanggal')
                ->select(
                    'o.tanggal',
                    DB::raw('COALESCE(o.omzet,0) as omzet'),
                    DB::raw('COALESCE(m.modal,0) as modal'),
                    DB::raw('COALESCE(p.pengeluaran,0) as pengeluaran')
                )
                ->orderByDesc('o.tanggal')
                ->get()
                ->map(function ($row) {
                    $row->laba_bersih = $row->omzet - $row->modal - $row->pengeluaran;
                    return $row;
                });

            // === Kirim ke DataTables ===
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y'))
                ->editColumn('omzet', fn($r) => 'Rp ' . number_format($r->omzet, 0, ',', '.'))
                ->editColumn('modal', fn($r) => 'Rp ' . number_format($r->modal, 0, ',', '.'))
                ->editColumn('pengeluaran', fn($r) => 'Rp ' . number_format($r->pengeluaran, 0, ',', '.'))
                ->editColumn('laba_bersih', fn($r) => 'Rp ' . number_format($r->laba_bersih, 0, ',', '.'))
                ->make(true);
        }
    }


    public function loadPengeluaranKategori(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('pengeluaran')
                ->select(
                    'kategori',
                    DB::raw('SUM(nominal) as total_pengeluaran')
                )
                ->groupBy('kategori')
                ->orderByDesc('total_pengeluaran')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('total_pengeluaran', fn($r) => 'Rp ' . number_format($r->total_pengeluaran, 0, ',', '.'))
                ->make(true);
        }
    }


    public function loadCashflow(Request $request)
    {
        if ($request->ajax()) {
            // === Kas Masuk (berasal dari transaksi yang sudah lunas) ===
            $kasMasuk = DB::table('transaksi')
                ->where('payment_status', 'lunas')
                ->select(
                    DB::raw('DATE(created_at) as tanggal'),
                    DB::raw('SUM(total) as kas_masuk')
                )
                ->groupBy(DB::raw('DATE(created_at)'));

            // === Kas Keluar (berasal dari tabel pengeluaran) ===
            $kasKeluar = DB::table('pengeluaran')
                ->select(
                    DB::raw('DATE(tanggal) as tanggal'),
                    DB::raw('SUM(nominal) as kas_keluar')
                )
                ->groupBy(DB::raw('DATE(tanggal)'));

            // === Gabungkan keduanya berdasarkan tanggal ===
            $data = DB::table(DB::raw("({$kasMasuk->toSql()}) as masuk"))
                ->mergeBindings($kasMasuk)
                ->leftJoinSub($kasKeluar, 'keluar', 'masuk.tanggal', '=', 'keluar.tanggal')
                ->select(
                    'masuk.tanggal',
                    DB::raw('COALESCE(masuk.kas_masuk,0) as kas_masuk'),
                    DB::raw('COALESCE(keluar.kas_keluar,0) as kas_keluar')
                )
                ->orderByDesc('masuk.tanggal')
                ->get()
                ->map(function ($row) {
                    $row->saldo_akhir = $row->kas_masuk - $row->kas_keluar;
                    return $row;
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('tanggal', fn($r) => \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y'))
                ->editColumn('kas_masuk', fn($r) => 'Rp ' . number_format($r->kas_masuk, 0, ',', '.'))
                ->editColumn('kas_keluar', fn($r) => 'Rp ' . number_format($r->kas_keluar, 0, ',', '.'))
                ->editColumn('saldo_akhir', fn($r) => 'Rp ' . number_format($r->saldo_akhir, 0, ',', '.'))
                ->make(true);
        }
    }

    public function loadRekapPembayaran(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('transaksi')
                ->select(
                    'payment_method',
                    DB::raw('COUNT(id) as jumlah_transaksi'),
                    DB::raw('SUM(total) as total_nominal')
                )
                ->where('payment_status', 'lunas')
                ->groupBy('payment_method')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('total_nominal', fn($r) => 'Rp ' . number_format($r->total_nominal, 0, ',', '.'))
                ->make(true);
        }
    }
}
