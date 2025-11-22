@extends('template.app')
@section('title', 'Stok Keluar')
@section('content')
    <div class="page-heading">
        <h3>Stok Keluar</h3>
    </div>
    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">Stok Keluar</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('kelola_data.PenguranganStok.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="PenguranganStok" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>Keterangan</th>
                                <th>Stok Keluar</th>
                                <th>Tanggal Input</th>
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
    <style>
        .right-gap {
            margin-right: 10px
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#PenguranganStok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.PenguranganStok.load') }}",
                    type: "POST",
                },
                pageLength: 10,
                searching: true,
                columns: [
                    {
                        data: 'barcode',
                        className: 'text-center',  // Align barcode column in center
                    },
                    {
                        data: 'name',
                        className: 'text-center',  // Align name column in center
                    },
                    {
                        data: 'category',
                        className: 'text-center',  // Align category column in center
                    },
                    {
                        data: 'selling_price',
                        render: function(data) {
                            // Format selling_price with thousands separator
                            return 'Rp ' + data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        },
                        className: 'text-center',  // Align selling price in center
                    },
                    {
                        data: 'keterangan',
                        className: 'text-center',  // Align tambah_stok column in center
                    },
                    {
                        data: 'kurang_stok',
                        className: 'text-center',  // Align tambah_stok column in center
                    },
                    {
                        data: 'tanggal_input',
                        className: 'text-center',  // Align column content in center

                    },

                ],
                columnDefs: [
                    {
                        className: "dt-head-center",
                        targets: ['_all']  // Center align headers
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 3, 4, 5]  // Center align body content of specific columns
                    }
                ],
                language: {
                    processing: "Memuat data...",  // Custom loading text
                    lengthMenu: "Tampilkan _MENU_ entri per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Pencarian:",
                }
            });


        });



    </script>
@endpush
