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
Route::get('/dashboard', [Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard-owner', [Dashboard::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard-owner');
Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::prefix('transaksi')->as('transaksi.')
        ->group(function () {
            Route::get('', [Transaksi::class, 'index'])->middleware('permission:14')->name('index');
            Route::get('/cariData', [Transaksi::class, 'cariData'])->middleware('permission:14')->name('cariData');
            Route::get('/products/search', [Transaksi::class, 'search'])->middleware('permission:14')->name('search');
            Route::get('/products/{id}', [Transaksi::class, 'getProductById'])->middleware('permission:14');
            Route::post('/save', [Transaksi::class, 'saveTransaction'])->middleware('permission:14');
            Route::get('/riwayat-transaksi', [Transaksi::class, 'showTransactionHistory'])->middleware('permission:14')->name('riwayat');
        });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-penjualan/export', [LaporanPenjualan::class, 'exportSalesReport'])->name('laporan.penjualan.export');

    // Permission
    Route::resource('akses', PermissionController::class)->middleware('permission:4');
    Route::post('akses/load', [PermissionController::class, 'load'])->middleware('permission:4')->name('akses.load');

    // Role
    Route::resource('peran', RoleController::class)->middleware('permission:5');
    Route::post('peran/load', [RoleController::class, 'load'])->middleware('permission:5')->name('peran.load');

    // User
    Route::resource('user', UserController::class)->middleware('permission:6');
    Route::post('user/load', [UserController::class, 'load'])->middleware('permission:6')->name('user.load');


    // Kelola Data
    Route::prefix('kelola_data')->as('kelola_data.')->group(function () {
        // SKPD

        // BPKB
        Route::prefix('products')->as('products.')
            ->group(function () {
                Route::get('', [ProductController::class, 'index'])->middleware('permission:8')->name('index');
                Route::post('load', [ProductController::class, 'load'])->middleware('permission:8')->name('load');
                Route::get('create', [ProductController::class, 'create'])->middleware('permission:8')->name('create');
                Route::post('store', [ProductController::class, 'store'])->middleware('permission:8')->name('store');
                Route::get('edit/{id}', [ProductController::class, 'edit'])->middleware('permission:8')->name('edit');
                Route::post('update/{id}', [ProductController::class, 'update'])->middleware('permission:8')->name('update');
                Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('permission:8')->name('destroy');
            });

        Route::prefix('PenerimaanStok')->as('PenerimaanStok.')
            ->group(function () {
                Route::get('', [PenerimaanStok::class, 'index'])->middleware('permission:9')->name('index');
                Route::post('load', [PenerimaanStok::class, 'load'])->middleware('permission:9')->name('load');
                Route::get('create', [PenerimaanStok::class, 'create'])->middleware('permission:9')->name('create');
                Route::get('edit/{id}', [PenerimaanStok::class, 'edit'])->middleware('permission:9')->name('edit');
                Route::delete('/delete/{id}', [PenerimaanStok::class, 'destroy'])->middleware('permission:9')->name('delete');
                Route::get('/search-product', [PenerimaanStok::class, 'searchProduct'])->middleware('permission:9')->name('search');
                Route::get('/product/barcode/{barcode}', [PenerimaanStok::class, 'getProductByBarcode'])->middleware('permission:9')->name('getByBarcode');
                Route::post('store', [PenerimaanStok::class, 'store'])->middleware('permission:9')->name('store');
            });

        Route::prefix('PenguranganStok')->as('PenguranganStok.')
            ->group(function () {
                Route::get('', [PenguranganStok::class, 'index'])->middleware('permission:10')->name('index');
                Route::post('load', [PenguranganStok::class, 'load'])->middleware('permission:10')->name('load');
                Route::get('create', [PenguranganStok::class, 'create'])->middleware('permission:10')->name('create');
                Route::get('edit/{id}', [PenguranganStok::class, 'edit'])->middleware('permission:10')->name('edit');
                Route::delete('/delete/{id}', [PenguranganStok::class, 'destroy'])->middleware('permission:10')->name('delete');
                Route::get('/search-product', [PenguranganStok::class, 'searchProduct'])->middleware('permission:10')->name('search');
                Route::get('/product/barcode/{barcode}', [PenguranganStok::class, 'getProductByBarcode'])->middleware('permission:10')->name('getByBarcode');
                Route::post('store', [PenguranganStok::class, 'store'])->middleware('permission:10')->name('store');
            });
    });


    Route::prefix('laporan')->as('laporan.')->group(function () {
        // BPKB
        Route::prefix('stok')->as('stok.')
            ->group(function () {
                Route::get('', [LaporanStok::class, 'index'])->middleware('permission:32')->name('index');
                Route::post('load', [LaporanStok::class, 'load'])->middleware('permission:32')->name('load');
            });

        // SERTIFIKAT
        Route::prefix('penjualan')->as('penjualan.')
            ->group(function () {
                Route::get('', [LaporanPenjualan::class, 'index'])->middleware('permission:33')->name('index');
                Route::post('load', [LaporanPenjualan::class, 'load'])->middleware('permission:33')->name('load');
                Route::get('/products', [LaporanPenjualan::class, 'getProducts'])->middleware('permission:33')->name('getProducts');
            });
    });
});

require __DIR__ . '/auth.php';
