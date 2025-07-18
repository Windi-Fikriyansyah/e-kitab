<?php

use App\Http\Controllers\KelolaAkses\PermissionController;
use App\Http\Controllers\KelolaAkses\RoleController;
use App\Http\Controllers\KelolaAkses\UserController;
use App\Http\Controllers\Transaksi;
use App\Http\Controllers\KelolaData\PenerimaanStok;
use App\Http\Controllers\KelolaData\ProductController;
use App\Http\Controllers\KelolaData\PenguranganStok;
use App\Http\Controllers\Laporan\LaporanStok;
use App\Http\Controllers\Laporan\LaporanPenjualan;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Laporan\SertifikatController as LaporanSertifikatController;
use App\Exports\SalesReportExport;
use App\Http\Controllers\KelolaData\CoverController;
use App\Http\Controllers\KelolaData\CustomerController;
use App\Http\Controllers\KelolaData\HarakatController;
use App\Http\Controllers\KelolaData\KategoriController;
use App\Http\Controllers\KelolaData\KertasController;
use App\Http\Controllers\KelolaData\KualitasController;
use App\Http\Controllers\KelolaData\PenerbitController;
use App\Http\Controllers\KelolaData\PenulisController;
use App\Http\Controllers\KelolaData\ProdukController;
use App\Http\Controllers\KelolaData\ProfilePerusahaan;
use App\Http\Controllers\KelolaData\SubKategoriController;
use App\Http\Controllers\KelolaData\SupplierController;
use App\Http\Controllers\KelolaData\UkuranController;
use App\Http\Controllers\KelolaLink\DeskripsiController;
use App\Http\Controllers\KelolaLink\GenerateController;
use App\Http\Controllers\Laporan\LaporanStokController;
use App\Http\Controllers\Laporan\LaporanSupplierController;
use App\Http\Controllers\Laporan\RekapSupplierController;
use App\Http\Controllers\Transaksi\BarangKeluarController;
use App\Http\Controllers\Transaksi\BarangMasukController;
use App\Http\Controllers\Transaksi\DataTransaksiController;
use App\Http\Controllers\Transaksi\TransaksiPenjualanController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [Dashboard::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');

// Route::middleware(['auth'])->group(function () {
//     Route::prefix('transaksi')->as('transaksi.')
//         ->group(function () {
//             Route::get('', [Transaksi::class, 'index'])->middleware('permission:14')->name('index');
//             Route::get('/cariData', [Transaksi::class, 'cariData'])->middleware('permission:14')->name('cariData');
//             Route::get('/products/search', [Transaksi::class, 'search'])->middleware('permission:14')->name('search');
//             Route::get('/products/{id}', [Transaksi::class, 'getProductById'])->middleware('permission:14');
//             Route::post('/save', [Transaksi::class, 'saveTransaction'])->middleware('permission:14');
//             Route::get('/riwayat-transaksi', [Transaksi::class, 'showTransactionHistory'])->middleware('permission:14')->name('riwayat');
//         });
// });

Route::middleware(['auth'])->group(function () {


    // Permission
    Route::resource('akses', PermissionController::class)->middleware('permission:2');
    Route::post('akses/load', [PermissionController::class, 'load'])->middleware('permission:2')->name('akses.load');

    // Role
    Route::resource('peran', RoleController::class)->middleware('permission:3');
    Route::post('peran/load', [RoleController::class, 'load'])->middleware('permission:3')->name('peran.load');

    // User
    Route::resource('user', UserController::class)->middleware('permission:4');
    Route::post('user/load', [UserController::class, 'load'])->middleware('permission:4')->name('user.load');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->middleware('permission:4')->name('user.destroy');


    // Kelola Data
    Route::prefix('kelola_data')->as('kelola_data.')->group(function () {


        Route::prefix('profile_perusahaan')->as('profile_perusahaan.')
            ->group(function () {
                Route::get('', [ProfilePerusahaan::class, 'index'])->middleware('permission:6')->name('index');
                Route::post('load', [ProfilePerusahaan::class, 'load'])->middleware('permission:6')->name('load');
                Route::get('create', [ProfilePerusahaan::class, 'create'])->middleware('permission:6')->name('create');
                Route::post('store', [ProfilePerusahaan::class, 'store'])->middleware('permission:6')->name('store');
                Route::get('edit/{id}', [ProfilePerusahaan::class, 'edit'])->middleware('permission:6')->name('edit');
                Route::put('update/{id}', [ProfilePerusahaan::class, 'update'])->middleware('permission:6')->name('update');
                Route::delete('/profile/{id}', [ProfilePerusahaan::class, 'destroy'])->middleware('permission:6')->name('destroy');
                Route::post('getsosmed', [ProfilePerusahaan::class, 'getSosmed'])->middleware('permission:6')->name('get_sosmed');
            });

        Route::prefix('customer')->as('customer.')
            ->group(function () {
                Route::get('', [CustomerController::class, 'index'])->middleware('permission:7')->name('index');
                Route::post('load', [CustomerController::class, 'load'])->middleware('permission:7')->name('load');
                Route::get('create', [CustomerController::class, 'create'])->middleware('permission:7')->name('create');
                Route::post('store', [CustomerController::class, 'store'])->middleware('permission:7')->name('store');
                Route::get('edit/{id}', [CustomerController::class, 'edit'])->middleware('permission:7')->name('edit');
                Route::put('update/{id}', [CustomerController::class, 'update'])->middleware('permission:7')->name('update');
                Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->middleware('permission:7')->name('destroy');
                Route::post('getsosmed', [CustomerController::class, 'getSosmed'])->middleware('permission:7')->name('get_sosmed');
            });

        Route::prefix('kategori')->as('kategori.')
            ->group(function () {
                Route::get('', [KategoriController::class, 'index'])->middleware('permission:8')->name('index');
                Route::post('load', [KategoriController::class, 'load'])->middleware('permission:8')->name('load');
                Route::get('create', [KategoriController::class, 'create'])->middleware('permission:8')->name('create');
                Route::post('store', [KategoriController::class, 'store'])->middleware('permission:8')->name('store');
                Route::get('edit/{id}', [KategoriController::class, 'edit'])->middleware('permission:8')->name('edit');
                Route::put('update/{id}', [KategoriController::class, 'update'])->middleware('permission:8')->name('update');
                Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->middleware('permission:8')->name('destroy');
            });

        Route::prefix('sub_kategori')->as('sub_kategori.')
            ->group(function () {
                Route::get('', [SubKategoriController::class, 'index'])->middleware('permission:34')->name('index');
                Route::post('load', [SubKategoriController::class, 'load'])->middleware('permission:34')->name('load');
                Route::get('create', [SubKategoriController::class, 'create'])->middleware('permission:34')->name('create');
                Route::post('store', [SubKategoriController::class, 'store'])->middleware('permission:34')->name('store');
                Route::get('edit/{id}', [SubKategoriController::class, 'edit'])->middleware('permission:34')->name('edit');
                Route::put('update/{id}', [SubKategoriController::class, 'update'])->middleware('permission:34')->name('update');
                Route::delete('/sub_kategori/{id}', [SubKategoriController::class, 'destroy'])->middleware('permission:34')->name('destroy');
                Route::post('/getkategori', [SubKategoriController::class, 'getkategori'])->middleware('permission:34')->name('getkategori');
            });

        Route::prefix('penerbit')->as('penerbit.')
            ->group(function () {
                Route::get('', [PenerbitController::class, 'index'])->middleware('permission:9')->name('index');
                Route::post('load', [PenerbitController::class, 'load'])->middleware('permission:9')->name('load');
                Route::get('create', [PenerbitController::class, 'create'])->middleware('permission:9')->name('create');
                Route::post('store', [PenerbitController::class, 'store'])->middleware('permission:9')->name('store');
                Route::get('edit/{id}', [PenerbitController::class, 'edit'])->middleware('permission:9')->name('edit');
                Route::put('update/{id}', [PenerbitController::class, 'update'])->middleware('permission:9')->name('update');
                Route::delete('/penerbit/{id}', [PenerbitController::class, 'destroy'])->middleware('permission:9')->name('destroy');
            });

        Route::prefix('harakat')->as('harakat.')
            ->group(function () {
                Route::get('', [HarakatController::class, 'index'])->middleware('permission:10')->name('index');
                Route::post('load', [HarakatController::class, 'load'])->middleware('permission:10')->name('load');
                Route::get('create', [HarakatController::class, 'create'])->middleware('permission:10')->name('create');
                Route::post('store', [HarakatController::class, 'store'])->middleware('permission:10')->name('store');
                Route::get('edit/{id}', [HarakatController::class, 'edit'])->middleware('permission:10')->name('edit');
                Route::put('update/{id}', [HarakatController::class, 'update'])->middleware('permission:10')->name('update');
                Route::delete('/harakat/{id}', [HarakatController::class, 'destroy'])->middleware('permission:10')->name('destroy');
            });

        Route::prefix('kertas')->as('kertas.')
            ->group(function () {
                Route::get('', [KertasController::class, 'index'])->middleware('permission:11')->name('index');
                Route::post('load', [KertasController::class, 'load'])->middleware('permission:11')->name('load');
                Route::get('create', [KertasController::class, 'create'])->middleware('permission:11')->name('create');
                Route::post('store', [KertasController::class, 'store'])->middleware('permission:11')->name('store');
                Route::get('edit/{id}', [KertasController::class, 'edit'])->middleware('permission:11')->name('edit');
                Route::put('update/{id}', [KertasController::class, 'update'])->middleware('permission:11')->name('update');
                Route::delete('/kertas/{id}', [KertasController::class, 'destroy'])->middleware('permission:11')->name('destroy');
            });

        Route::prefix('kualitas')->as('kualitas.')
            ->group(function () {
                Route::get('', [KualitasController::class, 'index'])->middleware('permission:12')->name('index');
                Route::post('load', [KualitasController::class, 'load'])->middleware('permission:12')->name('load');
                Route::get('create', [KualitasController::class, 'create'])->middleware('permission:12')->name('create');
                Route::post('store', [KualitasController::class, 'store'])->middleware('permission:12')->name('store');
                Route::get('edit/{id}', [KualitasController::class, 'edit'])->middleware('permission:12')->name('edit');
                Route::put('update/{id}', [KualitasController::class, 'update'])->middleware('permission:12')->name('update');
                Route::delete('/kualitas/{id}', [KualitasController::class, 'destroy'])->middleware('permission:12')->name('destroy');
            });

        Route::prefix('cover')->as('cover.')
            ->group(function () {
                Route::get('', [CoverController::class, 'index'])->middleware('permission:13')->name('index');
                Route::post('load', [CoverController::class, 'load'])->middleware('permission:13')->name('load');
                Route::get('create', [CoverController::class, 'create'])->middleware('permission:13')->name('create');
                Route::post('store', [CoverController::class, 'store'])->middleware('permission:13')->name('store');
                Route::get('edit/{id}', [CoverController::class, 'edit'])->middleware('permission:13')->name('edit');
                Route::put('update/{id}', [CoverController::class, 'update'])->middleware('permission:13')->name('update');
                Route::delete('/cover/{id}', [CoverController::class, 'destroy'])->middleware('permission:13')->name('destroy');
            });

        Route::prefix('supplier')->as('supplier.')
            ->group(function () {
                Route::get('', [SupplierController::class, 'index'])->middleware('permission:15')->name('index');
                Route::post('load', [SupplierController::class, 'load'])->middleware('permission:15')->name('load');
                Route::get('create', [SupplierController::class, 'create'])->middleware('permission:15')->name('create');
                Route::post('store', [SupplierController::class, 'store'])->middleware('permission:15')->name('store');
                Route::get('edit/{id}', [SupplierController::class, 'edit'])->middleware('permission:15')->name('edit');
                Route::put('update/{id}', [SupplierController::class, 'update'])->middleware('permission:15')->name('update');
                Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->middleware('permission:15')->name('destroy');
            });

        Route::prefix('penulis')->as('penulis.')
            ->group(function () {
                Route::get('', [PenulisController::class, 'index'])->middleware('permission:19')->name('index');
                Route::post('load', [PenulisController::class, 'load'])->middleware('permission:19')->name('load');
                Route::get('create', [PenulisController::class, 'create'])->middleware('permission:19')->name('create');
                Route::post('store', [PenulisController::class, 'store'])->middleware('permission:19')->name('store');
                Route::get('edit/{id}', [PenulisController::class, 'edit'])->middleware('permission:19')->name('edit');
                Route::put('update/{id}', [PenulisController::class, 'update'])->middleware('permission:19')->name('update');
                Route::delete('/supplier/{id}', [PenulisController::class, 'destroy'])->middleware('permission:19')->name('destroy');
            });

        Route::prefix('ukuran')->as('ukuran.')
            ->group(function () {
                Route::get('', [UkuranController::class, 'index'])->middleware('permission:30')->name('index');
                Route::post('load', [UkuranController::class, 'load'])->middleware('permission:30')->name('load');
                Route::get('create', [UkuranController::class, 'create'])->middleware('permission:30')->name('create');
                Route::post('store', [UkuranController::class, 'store'])->middleware('permission:30')->name('store');
                Route::get('edit/{id}', [UkuranController::class, 'edit'])->middleware('permission:30')->name('edit');
                Route::put('update/{id}', [UkuranController::class, 'update'])->middleware('permission:30')->name('update');
                Route::delete('/supplier/{id}', [UkuranController::class, 'destroy'])->middleware('permission:30')->name('destroy');
            });

        Route::prefix('produk')->as('produk.')
            ->group(function () {
                Route::get('', [ProdukController::class, 'index'])->middleware('permission:20')->name('index');
                Route::post('load', [ProdukController::class, 'load'])->middleware('permission:20')->name('load');
                Route::get('create', [ProdukController::class, 'create'])->middleware('permission:20')->name('create');
                Route::post('store', [ProdukController::class, 'store'])->middleware('permission:20')->name('store');
                Route::get('edit/{id}', [ProdukController::class, 'edit'])->middleware('permission:20')->name('edit');
                Route::get('show/{id}', [ProdukController::class, 'show'])->middleware('permission:20')->name('show');
                Route::put('update/{id}', [ProdukController::class, 'update'])->middleware('permission:20')->name('update');
                Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->middleware('permission:20')->name('destroy');
                Route::post('/getkategori', [ProdukController::class, 'getkategori'])->middleware('permission:20')->name('getkategori');
                Route::post('/getpenerbit', [ProdukController::class, 'getpenerbit'])->middleware('permission:20')->name('getpenerbit');
                Route::post('/getcover', [ProdukController::class, 'getcover'])->middleware('permission:20')->name('getcover');
                Route::post('produk/getkertas', [ProdukController::class, 'getkertas'])->name('getkertas');
                Route::post('produk/getkualitas', [ProdukController::class, 'getkualitas'])->name('getkualitas');
                Route::post('produk/getharakat', [ProdukController::class, 'getharakat'])->name('getharakat');
                Route::post('produk/getpenulis', [ProdukController::class, 'getpenulis'])->name('getpenulis');
                Route::post('produk/getsupplier', [ProdukController::class, 'getsupplier'])->name('getsupplier');
                Route::post('produk/getukuran', [ProdukController::class, 'getukuran'])->name('getukuran');

                Route::post('/kelola-data/produk/add-column', [ProdukController::class, 'addColumn'])->name('addColumn');
                Route::get('/kelola-data/produk/get-columns', [ProdukController::class, 'getDynamicColumns'])->name('getDynamicColumns');

                Route::post('/produk/delete-image', [ProdukController::class, 'deleteImage'])->name('deleteImage');
                Route::post('/getsubkategori', [ProdukController::class, 'getSubKategori'])->name('getsubkategori');
            });
    });


    Route::prefix('kelola_link')->as('kelola_link.')->group(function () {


        Route::prefix('deskripsi')->as('deskripsi.')
            ->group(function () {
                Route::get('', [DeskripsiController::class, 'index'])->middleware('permission:17')->name('index');
                Route::post('load', [DeskripsiController::class, 'load'])->middleware('permission:17')->name('load');
                Route::get('create', [DeskripsiController::class, 'create'])->middleware('permission:17')->name('create');
                Route::post('store', [DeskripsiController::class, 'store'])->middleware('permission:17')->name('store');
                Route::get('edit/{id}', [DeskripsiController::class, 'edit'])->middleware('permission:17')->name('edit');
                Route::put('update/{id}', [DeskripsiController::class, 'update'])->middleware('permission:17')->name('update');
                Route::delete('/profile/{id}', [DeskripsiController::class, 'destroy'])->middleware('permission:17')->name('destroy');
                Route::post('getsosmed', [DeskripsiController::class, 'getSosmed'])->middleware('permission:17')->name('get_sosmed');
            });

        Route::prefix('generate_deskripsi')->as('generate_deskripsi.')
            ->group(function () {
                Route::get('', [GenerateController::class, 'index'])->middleware('permission:18')->name('index');
                Route::post('load', [GenerateController::class, 'load'])->middleware('permission:18')->name('load');
                Route::get('create/{id}', [GenerateController::class, 'create'])->middleware('permission:18')->name('create');
                Route::post('store', [GenerateController::class, 'store'])->middleware('permission:18')->name('store');
                Route::get('edit/{id}', [GenerateController::class, 'edit'])->middleware('permission:18')->name('edit');
                Route::put('update/{id}', [GenerateController::class, 'update'])->middleware('permission:18')->name('update');
                Route::delete('/generatedeskripsi/{id}', [GenerateController::class, 'destroy'])->middleware('permission:18')->name('destroy');
                Route::post('produk/getformatwa', [GenerateController::class, 'getformatwa'])->name('getformatwa');
                Route::get('/get_produk_detail', [GenerateController::class, 'getProdukDetail'])->name('get_produk_detail');
                Route::get('/get_produk_Indo_detail', [GenerateController::class, 'getProdukDetailIndo'])->name('get_produk_Indo_detail');
                Route::POST('/getformat', [GenerateController::class, 'getformat'])->name('getformat');
            });
    });

    Route::prefix('transaksi')->as('transaksi.')->group(function () {


        Route::prefix('barang_masuk')->as('barang_masuk.')
            ->group(function () {
                Route::get('', [BarangMasukController::class, 'index'])->middleware('permission:22')->name('index');
                Route::post('load', [BarangMasukController::class, 'load'])->middleware('permission:22')->name('load');
                Route::get('create', [BarangMasukController::class, 'create'])->middleware('permission:22')->name('create');
                Route::post('store', [BarangMasukController::class, 'store'])->middleware('permission:22')->name('store');
                Route::get('edit/{id}', [BarangMasukController::class, 'edit'])->middleware('permission:22')->name('edit');
                Route::put('update/{id}', [BarangMasukController::class, 'update'])->middleware('permission:22')->name('update');
                Route::delete('/barang_masuk/{id}', [BarangMasukController::class, 'destroy'])->middleware('permission:22')->name('destroy');
                Route::post('/getproduk', [BarangMasukController::class, 'getproduk'])->middleware('permission:22')->name('getproduk');
            });

        Route::prefix('barang_keluar')->as('barang_keluar.')
            ->group(function () {
                Route::get('', [BarangKeluarController::class, 'index'])->middleware('permission:23')->name('index');
                Route::post('load', [BarangKeluarController::class, 'load'])->middleware('permission:23')->name('load');
                Route::get('create', [BarangKeluarController::class, 'create'])->middleware('permission:23')->name('create');
                Route::post('store', [BarangKeluarController::class, 'store'])->middleware('permission:23')->name('store');
                Route::get('edit/{id}', [BarangKeluarController::class, 'edit'])->middleware('permission:23')->name('edit');
                Route::put('update/{id}', [BarangKeluarController::class, 'update'])->middleware('permission:23')->name('update');
                Route::delete('/barang_keluar/{id}', [BarangKeluarController::class, 'destroy'])->middleware('permission:23')->name('destroy');
                Route::post('/getproduk', [BarangKeluarController::class, 'getproduk'])->middleware('permission:23')->name('getproduk');
            });

        Route::prefix('transaksi_penjualan')->as('transaksi_penjualan.')
            ->group(function () {
                Route::get('', [TransaksiPenjualanController::class, 'index'])->middleware('permission:24')->name('index');
                Route::post('load', [TransaksiPenjualanController::class, 'load'])->middleware('permission:24')->name('load');
                Route::get('create', [TransaksiPenjualanController::class, 'create'])->middleware('permission:24')->name('create');
                Route::post('store', [TransaksiPenjualanController::class, 'store'])->middleware('permission:24')->name('store');
                Route::get('edit/{id}', [TransaksiPenjualanController::class, 'edit'])->middleware('permission:24')->name('edit');
                Route::put('update/{id}', [TransaksiPenjualanController::class, 'update'])->middleware('permission:24')->name('update');
                Route::delete('/transaksi_penjualan/{id}', [TransaksiPenjualanController::class, 'destroy'])->middleware('permission:24')->name('destroy');
                Route::post('/getproduk', [TransaksiPenjualanController::class, 'getproduk'])->middleware('permission:24')->name('getproduk');
                Route::post('/getcustomer', [TransaksiPenjualanController::class, 'getcustomer'])->middleware('permission:24')->name('getcustomer');
                Route::post('/transaksi/penjualan/simpan-customer', [TransaksiPenjualanController::class, 'simpanCustomer'])
                    ->name('simpancustomer');
                Route::post('/transaksi/penjualan/simpan', [TransaksiPenjualanController::class, 'simpanTransaksi'])
                    ->name('simpan');
                Route::get('/transaksi/penjualan/cetak-invoice/{id}', [TransaksiPenjualanController::class, 'cetakInvoice'])
                    ->name('cetak_invoice');
            });

        Route::prefix('data_transaksi')->as('data_transaksi.')
            ->group(function () {
                Route::get('', [DataTransaksiController::class, 'index'])->middleware('permission:28')->name('index');
                Route::post('load', [DataTransaksiController::class, 'load'])->middleware('permission:28')->name('load');
                Route::get('create', [DataTransaksiController::class, 'create'])->middleware('permission:28')->name('create');
                Route::post('store', [DataTransaksiController::class, 'store'])->middleware('permission:28')->name('store');
                Route::get('edit/{id}', [DataTransaksiController::class, 'edit'])->middleware('permission:28')->name('edit');
                Route::get('detail/{id}', [DataTransaksiController::class, 'detail'])->middleware('permission:28')->name('detail');
                Route::put('update/{id}', [DataTransaksiController::class, 'update'])->middleware('permission:28')->name('update');
                Route::delete('/barang_keluar/{id}', [DataTransaksiController::class, 'destroy'])->middleware('permission:28')->name('destroy');
                Route::post('/getproduk', [DataTransaksiController::class, 'getproduk'])->middleware('permission:28')->name('getproduk');
                Route::post('/{id}/pay', [DataTransaksiController::class, 'pay'])->name('pay');
                Route::get('/payment-history/{id}', [DataTransaksiController::class, 'paymentHistory'])->name('payment_history');
                Route::get('/cetak-invoice/{id}', [DataTransaksiController::class, 'cetakInvoice'])
                    ->name('cetak_invoice');
            });
    });



    Route::prefix('laporan')->as('laporan.')->group(function () {
        Route::prefix('laporan-stok')->as('laporan-stok.')
            ->group(function () {
                Route::get('', [LaporanStokController::class, 'index'])->middleware('permission:26')->name('index');
                Route::post('load', [LaporanStokController::class, 'load'])->middleware('permission:26')->name('load');
                Route::post('filter', [LaporanStokController::class, 'filter'])->middleware('permission:26')->name('filter');
                Route::get('export', [LaporanStokController::class, 'export'])->middleware('permission:26')->name('export');
            });

        Route::prefix('laporan_supplier')->as('laporan_supplier.')
            ->group(function () {
                Route::get('', [LaporanSupplierController::class, 'index'])->middleware('permission:27')->name('index');
                Route::post('load', [LaporanSupplierController::class, 'load'])->middleware('permission:27')->name('load');
                Route::post('filter', [LaporanSupplierController::class, 'filter'])->middleware('permission:27')->name('filter');
                Route::get('export', [LaporanSupplierController::class, 'export'])->middleware('permission:27')->name('export');
            });

        Route::prefix('rekap_laporan_supplier')->as('rekap_laporan_supplier.')
            ->group(function () {
                Route::get('', [RekapSupplierController::class, 'index'])->middleware('permission:29')->name('index');
                Route::post('load', [RekapSupplierController::class, 'load'])->middleware('permission:29')->name('load');
                Route::post('filter', [RekapSupplierController::class, 'filter'])->middleware('permission:29')->name('filter');
                Route::get('export', [RekapSupplierController::class, 'export'])->middleware('permission:29')->name('export');
            });
        Route::prefix('laporan_penjualan')->as('laporan_penjualan.')
            ->group(function () {
                Route::get('', [LaporanPenjualan::class, 'index'])->middleware('permission:32')->name('index');
                Route::post('load', [LaporanPenjualan::class, 'load'])->middleware('permission:32')->name('load');
                Route::post('filter', [LaporanPenjualan::class, 'filter'])->middleware('permission:32')->name('filter');
                Route::get('export', [LaporanPenjualan::class, 'export'])->middleware('permission:32')->name('export');
            });
    });
});

require __DIR__ . '/auth.php';
