@extends('template.app')
@section('title', 'Data Produk')
@section('content')
    <div class="page-heading">
        <h3>Data Produk</h3>
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
                        <h5 class="card-title">Data Produk</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('kelola_data.products.create') }}" class="btn btn-success">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="products" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                                <th>Gambar</th>
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

            $('#products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.products.load') }}",
                    type: "POST",
                    // data: function(data) {
                    //     data.search = $('input[type="search"]').val();
                    // }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'barcode',
                    }, {
                        data: 'name',
                    }, {
                        data: 'category',
                    }, { data: 'purchase_price', render: function(data) {
        return data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    }},
    { data: 'selling_price', render: function(data) {
        return data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    }},{
                        data: 'stock',
                    },
                    {
                        data: 'satuan',
                        className: 'text-center'
                    },
                    { data: 'photo', className: 'text-center' },
                    {
                        data: 'aksi',
                        className: 'text-center'
                    }
                ],
                columnDefs: [

                    {
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 4,5]
                    }
                ]
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
                                    $('#products').DataTable().ajax.reload();
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
