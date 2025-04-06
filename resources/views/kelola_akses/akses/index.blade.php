@extends('template.app')
@section('title', 'Akses')
@section('content')
    <div class="page-heading">
        <h3>Kelola Akses</h3>
    </div>
    <div class="page-content">
        <section class="section">
            @if (session('message'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0">AKSES</h5>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('akses.create') }}" class="btn btn-success">Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="akses">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Link</th>
                                <th>Parent</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </section>
    </div>
@endsection
@push('js')
    <style>
        .right-gap {
            margin-right: 10px
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#akses').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('akses.load') }}",
                    type: "POST",
                    // data: function(data) {
                    //     data.search = $('input[type="search"]').val();
                    // }
                },
                pageLength: 10,
                searching: true,
                aoColumns: [{
                        data: 'name',
                    },
                    {
                        data: 'link',
                    }, {
                        data: 'parent',
                    }, {
                        data: 'aksi',
                        className: 'text-center'
                    }
                ]
            });
        });

        function hapus(id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success right-gap",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Tidak, kembali!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/akses/' + id,
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status == true) {
                                swalWithBootstrapButtons.fire({
                                    title: "Terhapus!",
                                    text: "Data berhasil dihapus!",
                                    icon: "success"
                                });
                                let tabel = $('#akses').DataTable();
                                tabel.ajax.reload();
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Gagal!",
                                    text: response.message,
                                    icon: "warning"
                                });
                            }
                        }
                    });

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Batal",
                        text: "Data tidak dihapus!",
                        icon: "error"
                    });
                }
            });
        }
    </script>
@endpush
