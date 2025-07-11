@extends('template.app')
@section('title', 'Laporan Supplier')
@section('content')

    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal"
                            value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="supplier" class="form-label">Supplier</label>
                        <select class="form-select select2-supplier" id="supplier" name="supplier">
                            <option value="">Semua Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="produk" class="form-label">Produk</label>
                        <select class="form-select select2-produk" id="produk" name="produk">
                            <option value="">Semua Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->kd_produk }}">{{ $product->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 d-flex gap-2">
                        <button type="button" class="btn btn-primary w-100" onclick="reloadTable()">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <button type="button" class="btn btn-success w-100" onclick="exportExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body p-2">
                                <h6 class="mb-0">Total Quantity Terjual: <span id="total-qty" class="fw-bold">0</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body p-2">
                                <h6 class="mb-0">Total Nilai Terjual: <span id="total-nilai" class="fw-bold">Rp 0</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="laporansupplier" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Supplier</th>
                                <th>Harga Beli</th>
                                <th>QTY Terjual</th>
                                <th>Total Terjual</th>
                                <th>Tanggal Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding-top: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-supplier').select2({
                placeholder: "Pilih Supplier",
                theme: "bootstrap-5",
                allowClear: true
            });

            $('.select2-produk').select2({
                placeholder: "Pilih Produk",
                theme: "bootstrap-5",
                allowClear: true
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            window.table = $('#laporansupplier').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.laporan_supplier.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.tanggal_awal = $('#tanggal_awal').val();
                        d.tanggal_akhir = $('#tanggal_akhir').val();
                        d.supplier = $('#supplier').val();
                        d.produk = $('#produk').val();
                    },
                    dataSrc: function(json) {
                        // Update totals when data is loaded
                        $('#total-qty').text(json.total_qty || 0);
                        $('#total-nilai').text('Rp ' + (json.total_nilai ? parseInt(json.total_nilai)
                            .toLocaleString('id-ID') : 0));
                        return json.data;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'kd_produk',
                        name: 'produk.kd_produk'
                    },
                    {
                        data: 'judul',
                        name: 'produk.judul'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'supplier.nama_supplier'
                    },
                    {
                        data: 'harga_modal',
                        name: 'produk.harga_modal',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'qty_terjual',
                        name: 'qty_terjual'
                    },
                    {
                        data: 'total_terjual',
                        name: 'total_terjual',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'transaksi.created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    }
                ],
                columnDefs: [{
                    className: "dt-head-center",
                    targets: ['_all']
                }]
            });
        });

        function reloadTable() {
            window.table.ajax.reload();
        }

        function exportExcel() {
            let tanggal_awal = $('#tanggal_awal').val();
            let tanggal_akhir = $('#tanggal_akhir').val();
            let supplier = $('#supplier').val();
            let produk = $('#produk').val();

            window.location.href = "{{ route('laporan.laporan_supplier.export') }}?tanggal_awal=" + tanggal_awal +
                "&tanggal_akhir=" + tanggal_akhir + "&supplier=" + supplier + "&produk=" + produk;
        }
    </script>
@endpush
