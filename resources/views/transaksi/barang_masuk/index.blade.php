@extends('template.app')
@section('title', 'Barang Masuk')
@section('content')
    <div class="page-heading">
        <h3>Barang Masuk</h3>
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
                        <h5 class="card-title">Barang Masuk</h5>
                    </div>
                    <div class="dropdown ms-auto">

                        <a href="{{ route('transaksi.barang_masuk.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="barang_masuk" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Kd Produk</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Penerbit</th>
                                <th>Supplier</th>
                                <th>Stok Masuk</th>
                                <th>Aksi</th>
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
        .right-gap {
            margin-right: 10px
        }

        .select2-container .select2-selection--single {
            height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#barang_masuk').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.barang_masuk.load') }}",
                    type: "POST",
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
                        data: 'penulis',
                        name: 'produk.penulis'
                    },
                    {
                        data: 'kategori',
                        name: 'produk.kategori'
                    },
                    {
                        data: 'penerbit',
                        name: 'produk.penerbit'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'supplier.nama_supplier'
                    },
                    {
                        data: 'stok_masuk',
                        name: 'barang_masuk.stok_masuk'
                    },
                    {
                        data: 'aksi',
                        className: 'text-center text-nowrap',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    className: "dt-head-center",
                    targets: ['_all']
                }],

            });




            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Barang Masuk ini akan dihapus!",
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
                                        'Barang Masuk berhasil dihapus.',
                                        'success'
                                    );
                                    $('#barang_masuk').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus barang_masuk.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus barang_masuk.',
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
