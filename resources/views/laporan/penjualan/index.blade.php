@extends('template.app')
@section('title', 'Laporan Penjualan')
@section('content')
<div class="page-heading">
    <h3>Laporan Penjualan</h3>
</div>
<div class="page-content">
    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" id="start_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="button" id="filterBtn" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('laporan.penjualan.export') }}" method="GET" class="mt-3">
                        <input type="hidden" name="start_date" id="export_start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" id="export_end_date" value="{{ request('end_date') }}">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>
                    </form>
                </div>
            </div>

            <!-- Export Button -->




            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="salesTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Nama Produk</th>
                            <th>Harga Jual</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            <th id="totalQty" class="text-center">0</th>
                            <th id="grandTotal" class="text-end">0</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Load products
    $('#filterBtn').click(function() {
        table.ajax.reload();

        // Update export form with the current filters
        $('#export_start_date').val($('#start_date').val());
        $('#export_end_date').val($('#end_date').val());
    });

    $.ajax({
        url: "{{ route('laporan.penjualan.getProducts') }}",
        type: "GET",
        success: function(response) {
            if (response.products) {
                response.products.forEach(function(product) {
                    $('#product_id').append(`<option value="${product.id}">${product.name}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading products:', error);
        }
    });

    // Initialize DataTable
    var table = $('#salesTable').DataTable({
    processing: true,
    serverSide: true,
    ordering: false,
    ajax: {
        url: "{{ route('laporan.penjualan.load') }}",
        type: "POST",
        data: function(d) {
            d.start_date = $('#start_date').val();
            d.end_date = $('#end_date').val();
            d._token = "{{ csrf_token() }}";
        },
        error: function(xhr, error, thrown) {
            console.error('Error:', error);
        }
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'text-center' },
        { data: 'created_at', name: 'created_at', class: 'text-center' },
        { data: 'nama_kasir', name: 'nama_kasir' },
        { data: 'nama_produk', name: 'nama_produk' },
        { data: 'selling_price', name: 'selling_price', class: 'text-end' },
        { data: 'Quantity', name: 'Quantity', class: 'text-center' },
        { data: 'total', name: 'total', class: 'text-end' }
    ],
    drawCallback: function(settings) {
        var api = this.api();

        // Calculate total quantity (Column 6: Quantity)
        var totalQty = api.column(5, {page:'current'}).data()
            .reduce(function(acc, val) {
                return acc + parseInt(val);
            }, 0);

        // Calculate grand total (Column 7: total)
        var grandTotal = api.column(6, {page:'current'}).data()
            .reduce(function(acc, val) {
                return acc + parseInt(val.replace(/[^\d]/g, ''));
            }, 0);

        // Update footer
        $('#totalQty').html(totalQty);
        $('#grandTotal').html(new Intl.NumberFormat('id-ID').format(grandTotal));
    }
});


    // Filter button click handler
    $('#filterBtn').click(function() {
        table.ajax.reload();
    });

    // Error handling
    $.fn.dataTable.ext.errMode = 'throw';
});
</script>
@endpush
