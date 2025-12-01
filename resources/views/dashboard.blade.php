@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="page-heading">
        <h3>Dashboard</h3>
    </div>

    <div class="page-content">
        @if (auth()->user()->role === '1')
            <section class="row">
                <div class="col-12 col-lg-12">
                    <div class="row">
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon purple mb-2">
                                                <i class="iconly-boldShow"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Omset Perhari</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($dailyIncome, 0, ',', '.') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon purple mb-2">
                                                <i class="iconly-boldShow"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Omset Perbulan</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($monthlyIncome, 0, ',', '.') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon red mb-2">
                                                <i class="iconly-boldBookmark"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Total Piutang</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($totalpiutang, 0, ',', '.') }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon blue mb-2">
                                                <i class="iconly-boldProfile"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Total Transaksi</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($totalTransactions) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon green mb-2">
                                                <i class="iconly-boldAdd-User"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Total Customer</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($totalCustomers) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon green mb-2">
                                                <i class="iconly-boldAdd-User"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Laba Perhari</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($labaPerHari, 0, ',', '.') }}</h6>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                            <div class="stats-icon green mb-2">
                                                <i class="iconly-boldAdd-User"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="text-muted font-semibold">Laba Perbulan</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($labaPerBulan, 0, ',', '.') }}</h6>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card card-clickable text-white shadow-sm border-0"
                                style="background-color: #059669; cursor: pointer; transition: all 0.3s;"
                                onclick="window.location='{{ route('transaksi.transaksi_penjualan.index') }}'">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon mb-2 d-flex align-items-center justify-content-center rounded"
                                                style="background-color: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                                                <i class="fa-solid fa-cash-register fa-lg text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="font-semibold mb-0 text-white">Input Transaksi</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card card-clickable text-white shadow-sm border-0"
                                style="background-color: #7e57c2; cursor: pointer; transition: all 0.3s;"
                                onclick="window.location='{{ route('kelola_data.produk.create') }}'">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon mb-2 d-flex align-items-center justify-content-center rounded"
                                                style="background-color: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                                                <i class="fa-solid fa-boxes-packing fa-lg text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="font-semibold mb-0 text-white">Input Produk</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card card-clickable text-white shadow-sm border-0"
                                style="background-color: #1d4ed8; cursor: pointer; transition: all 0.3s;"
                                onclick="window.location='{{ route('kelola_data.produk.index') }}'">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon mb-2 d-flex align-items-center justify-content-center rounded"
                                                style="background-color: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                                                <i class="fa-solid fa-box-open fa-lg text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="font-semibold mb-0 text-white">Data Produk</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card card-clickable text-white shadow-sm border-0"
                                style="background-color: #f59e0b; cursor: pointer; transition: all 0.3s;"
                                onclick="window.location='{{ route('kelola_data.customer.index') }}'">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon mb-2 d-flex align-items-center justify-content-center rounded"
                                                style="background-color: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                                                <i class="fa-solid fa-address-book fa-lg text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="font-semibold mb-0 text-white">Data Customer</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-lg-3 col-md-6">
                            <div class="card card-clickable text-white shadow-sm border-0"
                                style="background-color: #d97706; cursor: pointer; transition: all 0.3s;"
                                onclick="window.location='{{ route('transaksi.data_transaksi.index') }}'">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon mb-2 d-flex align-items-center justify-content-center rounded"
                                                style="background-color: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                                                <i class="fa-solid fa-receipt fa-lg text-white"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                            <h6 class="font-semibold mb-0 text-white">Data Transaksi</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>

                    <div class="row">
                        <div class="col-12 col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Produk Terlaris</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-lg">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Jumlah Terjual</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bestSellers as $product)
                                                    <tr>

                                                        <td class="col-auto">
                                                            <p class="font-bold mb-0">{{ $product->judul }}</p>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class="font-bold mb-0">{{ $product->kategori }}</p>
                                                        </td>
                                                        <td class="col-auto">
                                                            <p class="mb-0">{{ number_format($product->total_sold) }}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Barang Hampir Habis</h4>
                                </div>
                                <div class="card-body">
                                    @if ($lowStockItems->isEmpty())
                                        <div class="alert alert-success">
                                            Semua stok barang mencukupi.
                                        </div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-lg">
                                                <thead>
                                                    <tr>
                                                        <th>Sisa Stok</th>
                                                        <th>Nama Produk</th>
                                                        <th>Kategori</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($lowStockItems as $item)
                                                        <tr>
                                                            <td class="col-auto">
                                                                <span
                                                                    class="badge bg-danger">{{ number_format($item->stok) }}</span>
                                                            </td>
                                                            <td class="col-auto">
                                                                <p class="font-bold mb-0">{{ $item->judul }}</p>
                                                            </td>
                                                            <td class="col-auto">
                                                                <p class="font-bold mb-0">{{ $item->kategori }}</p>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4>Manajemen Stok</h4>
                                        <ul class="nav nav-tabs card-header-tabs" id="stokTab" role="tablist">
                                            <li class="nav-item"><button class="nav-link active" id="realTime-tab"
                                                    data-bs-toggle="tab" data-bs-target="#realTime" type="button"
                                                    role="tab">Stok Real-Time</button></li>
                                            <li class="nav-item"><button class="nav-link" id="riwayat-tab"
                                                    data-bs-toggle="tab" data-bs-target="#riwayat" type="button"
                                                    role="tab">Riwayat Masuk / Keluar</button></li>
                                            <li class="nav-item"><button class="nav-link" id="aging-tab"
                                                    data-bs-toggle="tab" data-bs-target="#aging" type="button"
                                                    role="tab">Aging Stok</button></li>
                                            <li class="nav-item"><button class="nav-link" id="pergerakan-tab"
                                                    data-bs-toggle="tab" data-bs-target="#pergerakan" type="button"
                                                    role="tab">Pergerakan per Produk</button></li>
                                        </ul>
                                    </div>

                                    <div class="card-body tab-content" id="stokTabContent">
                                        <!-- Stok Real-Time -->
                                        <div class="tab-pane fade show active" id="realTime" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableRealTime" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Produk</th>
                                                            <th>Kategori</th>
                                                            <th>Stok Saat Ini</th>
                                                            <th>Harga Jual</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Riwayat -->
                                        <div class="tab-pane fade" id="riwayat" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableRiwayat" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Produk</th>
                                                            <th>Masuk</th>
                                                            <th>Keluar</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Aging -->
                                        <div class="tab-pane fade" id="aging" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableAging" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Produk</th>
                                                            <th>Kategori</th>
                                                            <th>Stok</th>
                                                            <th>Usia Stok (Hari)</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Pergerakan -->
                                        <div class="tab-pane fade" id="pergerakan" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tablePergerakan" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Produk</th>
                                                            <th>Masuk (Bulan Ini)</th>
                                                            <th>Keluar (Bulan Ini)</th>
                                                            <th>Stok Akhir</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- ================== MANAGEMEN SUPPLIER ================== -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4>Manajemen Supplier</h4>
                                        <ul class="nav nav-tabs card-header-tabs" id="supplierTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="laporan-tab" data-bs-toggle="tab"
                                                    data-bs-target="#laporan" type="button" role="tab">
                                                    Laporan Barang Supplier
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tagihan-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tagihan" type="button" role="tab">
                                                    Tagihan Supplier
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ringkasan-tab" data-bs-toggle="tab"
                                                    data-bs-target="#ringkasan" type="button" role="tab">
                                                    Ringkasan Stok Supplier
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="card-body tab-content" id="supplierTabContent">
                                        <!-- Laporan Barang Supplier -->
                                        <div class="tab-pane fade show active" id="laporan" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableLaporanSupplier" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Supplier</th>
                                                            <th>Kode Produk</th>
                                                            <th>Judul</th>
                                                            <th>Kategori</th>
                                                            <th>Harga Modal</th>
                                                            <th>Harga Jual</th>
                                                            <th>Stok</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Tagihan Supplier -->
                                        <div class="tab-pane fade" id="tagihan" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableTagihanSupplier" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Supplier</th>
                                                            <th>Jumlah Produk</th>
                                                            <th>Total Tagihan (Rp)</th>
                                                            <th>Tanggal Transaksi Terakhir</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Ringkasan Stok Supplier -->
                                        <div class="tab-pane fade" id="ringkasan" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableRingkasanSupplier" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Supplier</th>
                                                            <th>Total Produk</th>
                                                            <th>Total Stok</th>
                                                            <th>Total Nilai Modal (Rp)</th>
                                                            <th>Total Nilai Jual (Rp)</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- ================== MANAJEMEN KEUANGAN ================== -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4>Manajemen Keuangan</h4>
                                        <ul class="nav nav-tabs card-header-tabs" id="keuanganTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="labaRugi-tab" data-bs-toggle="tab"
                                                    data-bs-target="#labaRugi" type="button" role="tab">Laba Rugi
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="pengeluaran-tab" data-bs-toggle="tab"
                                                    data-bs-target="#pengeluaran" type="button"
                                                    role="tab">Pengeluaran</button>
                                            </li>

                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="cashflow-tab" data-bs-toggle="tab"
                                                    data-bs-target="#cashflow" type="button" role="tab">Cashflow
                                                    Harian</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="rekap-tab" data-bs-toggle="tab"
                                                    data-bs-target="#rekap" type="button" role="tab">Rekap Metode
                                                    Pembayaran</button>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="card-body tab-content" id="keuanganTabContent">
                                        <!-- Laba Rugi -->
                                        <div class="tab-pane fade show active" id="labaRugi" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableLabaRugi" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Omzet (Rp)</th>
                                                            <th>Modal (Rp)</th>
                                                            <th>Pengeluaran (Rp)</th>
                                                            <th>Laba Bersih (Rp)</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>


                                        <!-- Pengeluaran -->
                                        <div class="tab-pane fade" id="pengeluaran" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tablePengeluaran" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Kategori</th>
                                                            <th>Total Pengeluaran</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Cashflow Harian -->
                                        <div class="tab-pane fade" id="cashflow" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableCashflow" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Kas Masuk (Rp)</th>
                                                            <th>Kas Keluar (Rp)</th>
                                                            <th>Saldo Akhir (Rp)</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Rekap Metode Pembayaran -->
                                        <div class="tab-pane fade" id="rekap" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="tableRekapPembayaran" class="table table-bordered w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Metode Pembayaran</th>
                                                            <th>Jumlah Transaksi</th>
                                                            <th>Total Nominal (Rp)</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </section>
        @else
            <div class="alert alert-danger">
                Anda tidak memiliki akses ke halaman ini.
            </div>
        @endif
    </div>
@endsection
@push('js')
    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const tables = {};

            function initTable(id, route, columns) {
                if (!tables[id]) {
                    tables[id] = $('#' + id).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: route,
                            type: 'POST'
                        },
                        columns: columns
                    });
                } else {
                    tables[id].ajax.reload();
                }
            }

            // === EVENT: Load data hanya saat tab aktif diklik ===
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('data-bs-target');

                switch (target) {
                    // ===================== STOK =====================
                    case '#realTime':
                        initTable('tableRealTime', "{{ route('dashboard.loadRealtime') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'judul'
                            }, {
                                data: 'kategori'
                            },
                            {
                                data: 'stok',
                                className: 'text-center'
                            },
                            {
                                data: 'harga_jual',
                                className: 'text-end'
                            }
                        ]);
                        break;
                    case '#riwayat':
                        initTable('tableRiwayat', "{{ route('dashboard.loadRiwayat') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'tanggal'
                            }, {
                                data: 'judul'
                            },
                            {
                                data: 'stok_masuk'
                            }, {
                                data: 'stok_keluar'
                            }, {
                                data: 'notes'
                            }
                        ]);
                        break;
                    case '#aging':
                        initTable('tableAging', "{{ route('dashboard.loadAging') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'judul'
                            }, {
                                data: 'kategori'
                            },
                            {
                                data: 'stok'
                            }, {
                                data: 'usia_stok'
                            }
                        ]);
                        break;
                    case '#pergerakan':
                        initTable('tablePergerakan', "{{ route('dashboard.loadPergerakan') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'judul'
                            }, {
                                data: 'total_masuk'
                            },
                            {
                                data: 'total_keluar'
                            }, {
                                data: 'stok'
                            }
                        ]);
                        break;

                        // ===================== SUPPLIER =====================
                    case '#laporan':
                        initTable('tableLaporanSupplier', "{{ route('dashboard.loadLaporanSupplier') }}",
                            [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'supplier'
                                }, {
                                    data: 'kd_produk'
                                }, {
                                    data: 'judul'
                                },
                                {
                                    data: 'kategori'
                                },
                                {
                                    data: 'harga_modal',
                                    className: 'text-end'
                                },
                                {
                                    data: 'harga_jual',
                                    className: 'text-end'
                                },
                                {
                                    data: 'stok',
                                    className: 'text-center'
                                }
                            ]);
                        break;
                    case '#tagihan':
                        initTable('tableTagihanSupplier', "{{ route('dashboard.loadTagihanSupplier') }}",
                            [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'nama_supplier'
                                },
                                {
                                    data: 'jumlah_produk',
                                    className: 'text-center'
                                },
                                {
                                    data: 'total_tagihan',
                                    className: 'text-end'
                                },
                                {
                                    data: 'tanggal_terakhir'
                                }
                            ]);
                        break;
                    case '#ringkasan':
                        initTable('tableRingkasanSupplier',
                            "{{ route('dashboard.loadRingkasanSupplier') }}", [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'nama_supplier'
                                },
                                {
                                    data: 'total_produk',
                                    className: 'text-center'
                                },
                                {
                                    data: 'total_stok',
                                    className: 'text-center'
                                },
                                {
                                    data: 'total_modal',
                                    className: 'text-end'
                                },
                                {
                                    data: 'total_jual',
                                    className: 'text-end'
                                }
                            ]);
                        break;

                        // ===================== KEUANGAN =====================
                    case '#labaRugi':
                        initTable('tableLabaRugi', "{{ route('dashboard.loadLabaRugi') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'tanggal'
                            },
                            {
                                data: 'omzet',
                                className: 'text-end'
                            },
                            {
                                data: 'modal',
                                className: 'text-end'
                            },
                            {
                                data: 'pengeluaran',
                                className: 'text-end'
                            },
                            {
                                data: 'laba_bersih',
                                className: 'text-end'
                            }
                        ]);
                        break;
                    case '#pengeluaran':
                        initTable('tablePengeluaran', "{{ route('dashboard.loadPengeluaranKategori') }}",
                            [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'kategori'
                                },
                                {
                                    data: 'total_pengeluaran',
                                    className: 'text-end'
                                }
                            ]);
                        break;
                    case '#cashflow':
                        initTable('tableCashflow', "{{ route('dashboard.loadCashflow') }}", [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'tanggal'
                            },
                            {
                                data: 'kas_masuk',
                                className: 'text-end'
                            },
                            {
                                data: 'kas_keluar',
                                className: 'text-end'
                            },
                            {
                                data: 'saldo_akhir',
                                className: 'text-end'
                            }
                        ]);
                        break;
                    case '#rekap':
                        initTable('tableRekapPembayaran', "{{ route('dashboard.loadRekapPembayaran') }}",
                            [{
                                    data: 'DT_RowIndex',
                                    orderable: false,
                                    searchable: false
                                },
                                {
                                    data: 'payment_method'
                                },
                                {
                                    data: 'jumlah_transaksi',
                                    className: 'text-center'
                                },
                                {
                                    data: 'total_nominal',
                                    className: 'text-end'
                                }
                            ]);
                        break;
                }
            });

            // Load tab default pertama kali (agar tidak kosong)
            const stokActive = $('#stokTab button.active').attr('data-bs-target');
            if (stokActive) $(stokActive + '-tab').trigger('shown.bs.tab');

            // Tab Manajemen Supplier (default aktif: #laporan)
            const supplierActive = $('#supplierTab button.active').attr('data-bs-target');
            if (supplierActive) $(supplierActive + '-tab').trigger('shown.bs.tab');

            // Tab Manajemen Keuangan (default aktif: #labaRugi)
            const keuanganActive = $('#keuanganTab button.active').attr('data-bs-target');
            if (keuanganActive) $(keuanganActive + '-tab').trigger('shown.bs.tab');
        });
    </script>
@endpush

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .card-clickable {
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: none;
        }

        .card-clickable:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
            filter: brightness(1.05);
        }

        .card-clickable.bg-success {
            background: linear-gradient(135deg, #16a34a, #22c55e);
        }

        .card-clickable.bg-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
        }

        .card-clickable.bg-warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .card-clickable.bg-danger {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .stats-icon i {
            font-size: 1.6rem;
        }
    </style>
@endpush
