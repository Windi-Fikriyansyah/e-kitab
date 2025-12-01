@extends('template.app')
@section('title', 'Laporan Stok')
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
                        <label for="produk" class="form-label">Produk</label>

                        <div class="d-flex gap-2">
                            <select class="form-select select2-produk" id="produk" name="produk" style="flex: 1;">
                                <option value="">Semua Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->kd_produk }}">{{ $product->judul }}</option>
                                @endforeach
                            </select>

                            <button type="button" class="btn btn-primary" onclick="reloadTable()">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>

                </div>


            </div>

            <div class="card-body">
                <div class="mb-3">
                    <h5>Total Aset Keseluruhan: <span id="total-aset-keseluruhan">Rp 0</span></h5>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="laporansupplier" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Penerbit</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Total Aset</th>
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
                    url: "{{ route('laporan.rekap_laporan_supplier.load_stok') }}",
                    type: "POST",
                    data: function(d) {
                        d.tanggal_awal = $('#tanggal_awal').val();
                        d.tanggal_akhir = $('#tanggal_akhir').val();
                        d.produk = $('#produk').val();
                    },
                    dataSrc: function(json) {
                        $('#total-qty').text(json.total_qty || 0);
                        $('#total-nilai').text('Rp ' + (json.total_nilai ? parseInt(json.total_nilai)
                            .toLocaleString('id-ID') : 0));
                        $('#total-aset-keseluruhan').text(
                            'Rp ' + (json.total_aset_keseluruhan ?
                                parseInt(json.total_aset_keseluruhan).toLocaleString('id-ID') :
                                0)
                        );
                        return json.data;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'judul',
                        name: 'produk.judul'
                    },
                    {
                        data: 'penerbit',
                        name: 'produk.penerbit'
                    },
                    {
                        data: 'stok',
                        name: 'produk.stok'
                    },
                    {
                        data: 'harga_modal',
                        name: 'produk.harga_modal',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'total_aset',
                        name: 'total_aset',
                        render: function(data) {
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
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
            let produk = $('#produk').val();

            window.location.href = "{{ route('laporan.rekap_laporan_supplier.export') }}?tanggal_awal=" + tanggal_awal +
                "&tanggal_akhir=" + tanggal_akhir + "&produk=" + produk;
        }
    </script>
@endpush
