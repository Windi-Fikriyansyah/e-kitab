@extends('template.app')
@section('title', 'Laporan Penjualan')
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
                        <label for="kasir" class="form-label">Kasir</label>
                        <select class="form-select select2-kasir" id="kasir" name="kasir">
                            <option value="">Semua Kasir</option>
                            @foreach ($kasirs as $kasir)
                                <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="customer" class="form-label">Customer</label>
                        <select class="form-select select2-customer" id="customer" name="customer">
                            <option value="">Semua Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->nama }}">{{ $customer->nama }}</option>
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
                                <h6 class="mb-0">Total Transaksi: <span id="total-transaksi" class="fw-bold">0</span>
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
                    <table class="table align-middle mb-0" id="laporan-penjualan" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Tanggal Transaksi</th>
                                <th>Kode Transaksi</th>
                                <th>Customer</th>
                                <th>Metode Pembayaran</th>
                                <th>Status Pembayaran</th>
                                <th>Subtotal</th>
                                <th>Diskon</th>
                                <th>Potongan</th>
                                <th>Total</th>
                                <th>Kasir</th>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-kasir').select2({
                placeholder: "Pilih Kasir",
                theme: "bootstrap-5",
                allowClear: true
            });

            $('.select2-customer').select2({
                placeholder: "Pilih Customer",
                theme: "bootstrap-5",
                allowClear: true
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            window.table = $('#laporan-penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.laporan_penjualan.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.tanggal_awal = $('#tanggal_awal').val();
                        d.tanggal_akhir = $('#tanggal_akhir').val();
                        d.kasir = $('#kasir').val();
                        d.customer = $('#customer').val();
                    },
                    dataSrc: function(json) {
                        // Update totals when data is loaded
                        $('#total-transaksi').text(json.total_transaksi || 0);
                        $('#total-nilai').text('Rp ' + (json.total_nilai ?
                            parseInt(json.total_nilai).toLocaleString('id-ID') : 0));
                        return json.data;
                    }
                },
                pageLength: 10,
                searching: true,
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'created_at',
                        name: 'transaksi.created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'kode_transaksi',
                        name: 'transaksi.kode_transaksi'
                    },
                    {
                        data: 'nama_customer',
                        name: 'transaksi.nama_customer',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'payment_method',
                        name: 'transaksi.payment_method'
                    },
                    {
                        data: 'payment_status',
                        name: 'transaksi.payment_status'
                    },
                    {
                        data: 'subtotal',
                        name: 'transaksi.subtotal',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'discount',
                        name: 'transaksi.discount',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'potongan',
                        name: 'transaksi.potongan',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'total',
                        name: 'transaksi.total',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'kasir_name',
                        name: 'users.name'
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
            let kasir = $('#kasir').val();
            let customer = $('#customer').val();

            window.location.href = "{{ route('laporan.laporan_penjualan.export') }}?tanggal_awal=" + tanggal_awal +
                "&tanggal_akhir=" + tanggal_akhir + "&kasir=" + kasir + "&customer=" + customer;
        }
    </script>
@endpush
