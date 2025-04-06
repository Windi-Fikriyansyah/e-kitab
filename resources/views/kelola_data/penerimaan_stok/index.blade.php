@extends('template.app')
@section('title', 'Penerimaan Stok')
@section('content')
    <div class="page-heading">
        <h3>Penerimaan Stok</h3>
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
                        <h5 class="card-title">Penerimaan Stok</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('kelola_data.PenerimaanStok.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="PenerimaanStok" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Penambahan Stok</th>
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

            $('#PenerimaanStok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.PenerimaanStok.load') }}",
                    type: "POST",
                    data: function (d) {
                        d._token = "{{ csrf_token() }}"; // Tambahkan token CSRF
                    },
                },
                pageLength: 10,
                searching: true,
                columns: [
                    {
                        data: 'barcode',
                        className: 'text-center',  // Align barcode column in center
                    },
                    {
                        data: 'nama_produk',
                        className: 'text-center',  // Align name column in center
                    },
                    {
                        data: 'kategori',
                        className: 'text-center',  // Align category column in center
                    },
                    {
                        data: 'harga_beli',
                        render: function(data) {
                            // Format purchase_price with thousands separator
                            return 'Rp ' + data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        },
                        className: 'text-center',  // Align purchase price in center
                    },
                    {
                        data: 'harga_jual',
                        render: function(data) {
                            // Format selling_price with thousands separator
                            return 'Rp ' + data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        },
                        className: 'text-center',  // Align selling price in center
                    },
                    {
                        data: 'tambah_stok',
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
                        targets: [0, 1, 2, 3, 4, 5, 6]  // Center align body content of specific columns
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

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Produk ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Produk berhasil dihapus.',
                                        'success'
                                    );
                                    $('#PenerimaanStok').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus produk.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus produk.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });



    </script>
@endpush
