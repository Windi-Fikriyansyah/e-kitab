@extends('template.app')
@section('title', 'Riwayat Produk')
@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between">
            <h3>Riwayat Produk ({{ $produk->kd_produk }} - {{ $produk->judul }})</h3>
            <a href="{{ route('kelola_data.produk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        {{-- @if (isset($produk))
            <div class="mt-3">
                <h5>Detail Produk:</h5>
                <p><strong>Kode:</strong> {{ $produk->kd_produk }} | <strong>Judul:</strong> {{ $produk->judul }}</p>
            </div>
        @endif --}}
    </div>

    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Riwayat Stok Produk</h5>
                    <div class="d-flex">
                        <form id="formExportPdf" method="POST" action="{{ route('kelola_data.produk.exportPdf') }}"
                            target="_blank" class="me-2">
                            @csrf
                            <input type="hidden" name="id_produk" value="{{ $produk->id_produk_asli }}">
                            <input type="hidden" name="tanggal_awal" id="pdf_tanggal_awal">
                            <input type="hidden" name="tanggal_akhir" id="pdf_tanggal_akhir">
                            <input type="hidden" name="type" id="pdf_type">
                            <input type="hidden" name="user" id="pdf_user">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </form>

                        <form id="formExportExcel" method="POST" action="{{ route('kelola_data.produk.exportExcel') }}">
                            @csrf
                            <input type="hidden" name="id_produk" value="{{ $produk->id_produk_asli }}">
                            <input type="hidden" name="tanggal_awal" id="excel_tanggal_awal">
                            <input type="hidden" name="tanggal_akhir" id="excel_tanggal_akhir">
                            <input type="hidden" name="type" id="excel_type">
                            <input type="hidden" name="user" id="excel_user">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="card-body">


                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filter_tanggal_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" id="filter_tanggal_awal" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_tanggal_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="filter_tanggal_akhir" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_type" class="form-label">Type</label>
                        <select id="filter_type" class="form-control">
                            <option value="">-- Semua --</option>
                            <option value="Masuk">Masuk</option>
                            <option value="Keluar">Keluar</option>
                            <option value="Transaksi">Transaksi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_user" class="form-label">User</label>
                        <input type="text" id="filter_user" class="form-control" placeholder="Cari user...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="btnFilter" class="btn btn-primary me-2">Filter</button>
                        <button id="btnReset" class="btn btn-secondary">Reset</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="riwayat" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>User</th>
                                <th>Note</th>
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

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var idProduk = "{{ $produk->id_produk_asli ?? '' }}";

            var table = $('#riwayat').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.produk.load_riwayat') }}",
                    type: "POST",
                    data: function(d) {
                        d.id_produk = idProduk;
                        d.tanggal_awal = $('#filter_tanggal_awal').val();
                        d.tanggal_akhir = $('#filter_tanggal_akhir').val();
                        d.type = $('#filter_type').val();
                        d.user = $('#filter_user').val();
                    }
                },
                pageLength: 10,
                searching: false,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'jam',
                        name: 'jam'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'text-center'
                    },
                    {
                        data: 'user',
                        name: 'user',
                        defaultContent: '-'
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        defaultContent: '-'
                    }
                ],
                columnDefs: [{
                        className: "dt-head-center",
                        targets: '_all'
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 3, 4, 5]
                    }
                ]
            });
            $('#btnFilter').on('click', function() {
                table.ajax.reload();
            });

            // Reset filter
            $('#btnReset').on('click', function() {
                $('#filter_tanggal_awal').val('');
                $('#filter_tanggal_akhir').val('');
                $('#filter_type').val('');
                $('#filter_user').val('');
                table.ajax.reload();
            });

            $('#btnFilter, #btnReset').on('click', function() {
                // Sync PDF form
                $('#pdf_tanggal_awal').val($('#filter_tanggal_awal').val());
                $('#pdf_tanggal_akhir').val($('#filter_tanggal_akhir').val());
                $('#pdf_type').val($('#filter_type').val());
                $('#pdf_user').val($('#filter_user').val());

                // Sync Excel form
                $('#excel_tanggal_awal').val($('#filter_tanggal_awal').val());
                $('#excel_tanggal_akhir').val($('#filter_tanggal_akhir').val());
                $('#excel_type').val($('#filter_type').val());
                $('#excel_user').val($('#filter_user').val());

                table.ajax.reload();
            });

        });
    </script>
@endpush
