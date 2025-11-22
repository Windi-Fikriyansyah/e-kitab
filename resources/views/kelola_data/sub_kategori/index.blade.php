@extends('template.app')
@section('title', 'Sub Kategori')
@section('content')
    <div class="page-heading">
        <h3>Sub Kategori</h3>
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
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">Sub Kategori</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <a href="{{ route('kelola_data.sub_kategori.create') }}" class="btn btn-success">Tambah</a>


                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="sub_kategori" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th style="width: 35%;">Nama Kategori</th>
                                <th style="width: 35%;">الفنون</th>
                                <th style="width: 35%;">Nama Sub Kategori (Indonesia)</th>
                                <th style="width: 25%;">Aksi</th>
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

        .logo-img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 5px;
        }

        .sosmed-btn {
            cursor: pointer;
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

            $('#sub_kategori').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.sub_kategori.load') }}",
                    type: "POST",
                    data: function(d) {
                        // Send additional parameters if needed
                        d.search_value = d.search.value;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'kategori.nama_arab',
                        width: '35%',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_arab',
                        name: 'sub_kategori.nama_arab',
                        width: '35%',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_indonesia',
                        name: 'sub_kategori.nama_indonesia',
                        width: '35%',
                        className: 'text-center'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        width: '25%',
                        className: 'text-center'
                    }
                ],
                language: {
                    search: "Cari:",
                    searchPlaceholder: "Masukkan kata kunci..."
                }
            });



            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus!",
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
                                        'Data berhasil dihapus.',
                                        'success'
                                    );
                                    $('#sub_kategori').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus data.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus data.',
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
