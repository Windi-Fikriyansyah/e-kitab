@extends('template.app')
@section('title', 'Laporan Pengeluaran')
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
                    <div class="col-md-4">
                        <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggal_awal" value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggal_akhir" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select select2-kategori" id="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriList as $kat)
                                <option value="{{ $kat->kategori }}">{{ $kat->kategori }}</option>
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
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body p-2 text-center">
                                <h6 class="mb-1">Total Pengeluaran:
                                    <span id="total-nominal" class="fw-bold text-danger">Rp 0</span>
                                </h6>
                                <h6 class="mb-1">Total Pemasukan:
                                    <span id="total-pemasukan" class="fw-bold text-success">Rp 0</span>
                                </h6>
                                <h6 class="mb-0">Laba Rugi:
                                    <span id="total-laba-rugi" class="fw-bold text-primary">Rp 0</span>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="laporanpengeluaran" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Nominal</th>
                                <th>Metode Bayar</th>
                                <th>Lampiran</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2-kategori').select2({
                placeholder: "Pilih Kategori",
                theme: "bootstrap-5",
                allowClear: true
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            window.table = $('#laporanpengeluaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.laporan_pengeluaran.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.tanggal_awal = $('#tanggal_awal').val();
                        d.tanggal_akhir = $('#tanggal_akhir').val();
                        d.kategori = $('#kategori').val();
                    },
                    dataSrc: function(json) {
                        $('#total-nominal').text('Rp ' + (json.total_nominal ? parseInt(json
                            .total_nominal).toLocaleString('id-ID') : 0));
                        $('#total-pemasukan').text('Rp ' + (json.total_pemasukan ? parseInt(json
                            .total_pemasukan).toLocaleString('id-ID') : 0));
                        $('#total-laba-rugi').text('Rp ' + (json.laba_rugi ? parseInt(json.laba_rugi)
                            .toLocaleString('id-ID') : 0));
                        return json.data;
                    }

                },
                pageLength: 10,
                columns: [{
                        data: 'tanggal',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'kategori'
                    },
                    {
                        data: 'deskripsi'
                    },
                    {
                        data: 'nominal',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'metode_bayar'
                    },
                    {
                        data: 'lampiran',
                        render: function(data) {
                            if (!data) return '-';
                            return `<a href="/storage/${data}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>`;
                        }
                    },
                ]
            });
        });

        function reloadTable() {
            window.table.ajax.reload();
        }

        function exportExcel() {
            let tanggal_awal = $('#tanggal_awal').val();
            let tanggal_akhir = $('#tanggal_akhir').val();
            let kategori = $('#kategori').val();
            window.location.href = "{{ route('laporan.laporan_pengeluaran.export') }}?tanggal_awal=" + tanggal_awal +
                "&tanggal_akhir=" + tanggal_akhir + "&kategori=" + kategori;
        }
    </script>
@endpush
