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
                                            <h6 class="text-muted font-semibold">Total Pendapatan</h6>
                                            <h6 class="font-extrabold mb-0">Rp
                                                {{ number_format($totalIncome, 0, ',', '.') }}
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
                                                            <p class="mb-0">{{ number_format($product->total_sold) }}</p>
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

@push('scripts')
@endpush
