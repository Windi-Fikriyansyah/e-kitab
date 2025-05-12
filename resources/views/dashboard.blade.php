@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="page-heading">
        <h3>Dashboard</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-12">
                <!-- Filter Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Filter Data</h4>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" action="{{ route('dashboard-owner') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input period-checkbox" type="checkbox" name="period"
                                            id="today" value="today"
                                            {{ request('period') == 'today' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="today">
                                            Hari Ini
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input period-checkbox" type="checkbox" name="period"
                                            id="month" value="month"
                                            {{ !request('period') || request('period') == 'month' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="month">
                                            Bulan Ini
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dateRange">Periode Kustom</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="start_date" id="start_date"
                                                value="{{ request('start_date') }}">
                                            <span class="input-group-text">s/d</span>
                                            <input type="date" class="form-control" name="end_date" id="end_date"
                                                value="{{ request('end_date') }}">
                                            <button id="customFilterBtn" type="submit"
                                                class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldShow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Omset</h6>
                                        <h6 class="font-extrabold mb-0">Rp {{ number_format($omset, 0, ',', '.') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Pengeluaran</h6>
                                        <h6 class="font-extrabold mb-0">Rp {{ number_format($pengeluaran, 0, ',', '.') }}
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
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Profit</h6>
                                        <h6 class="font-extrabold mb-0">Rp {{ number_format($profit, 0, ',', '.') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Jumlah Transaksi</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($jumlahTransaksi, 0, ',', '.') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Selling Products -->
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Barang Terlaris</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Terjual</th>
                                                <th>Total Omset</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bestSelling as $key => $product)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->total_quantity }}</td>
                                                    <td>Rp {{ number_format($product->total_sales, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Products -->
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Stok Menipis</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Stok Tersedia</th>
                                                <th>Minimum Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lowStockProducts as $key => $product)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td
                                                        class="{{ $product->stock <= $product->minimum_stock ? 'text-danger fw-bold' : '' }}">
                                                        {{ $product->stock }}
                                                    </td>
                                                    <td>{{ $product->minimum_stock }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all checkboxes
            const periodCheckboxes = document.querySelectorAll('.period-checkbox');

            // Ensure only one checkbox can be selected at a time (like radio buttons)
            periodCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Uncheck other checkboxes
                        periodCheckboxes.forEach(otherCheckbox => {
                            if (otherCheckbox !== this) {
                                otherCheckbox.checked = false;
                            }
                        });

                        // Clear date range fields when selecting a preset period
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';

                        // Submit the form automatically after a slight delay
                        setTimeout(function() {
                            document.getElementById('filterForm').submit();
                        }, 100); // Small delay for better UX
                    } else {
                        // If unchecked and no other period is selected, default to month
                        let anyChecked = false;
                        periodCheckboxes.forEach(cb => {
                            if (cb.checked) anyChecked = true;
                        });

                        if (!anyChecked) {
                            document.getElementById('month').checked = true;
                            setTimeout(function() {
                                document.getElementById('filterForm').submit();
                            }, 100);
                        }
                    }
                });
            });

            // Set up event listeners for date inputs
            const dateInputs = [document.getElementById('start_date'), document.getElementById('end_date')];

            dateInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Uncheck all period checkboxes when using custom date range
                    periodCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check if both dates are filled
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;

                    // Auto-submit form only if both dates are selected
                    if (startDate && endDate) {
                        document.getElementById('filterForm').submit();
                    }
                });
            });

            // Make the custom filter button visible only when needed
            document.getElementById('customFilterBtn').style.display = 'none';

            // Show button only when one date is filled but not both
            dateInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;

                    // Show button if one field is filled but not both
                    if ((startDate && !endDate) || (!startDate && endDate)) {
                        document.getElementById('customFilterBtn').style.display = 'block';
                    } else {
                        document.getElementById('customFilterBtn').style.display = 'none';
                    }
                });
            });
        });
    </script>
@endpush
