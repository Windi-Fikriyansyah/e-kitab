@extends('template.app')
@section('title', 'Profile Perusahaan')
@section('content')
    <div class="page-heading">
        <h3>Profile Perusahaan</h3>
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
                        <h5 class="card-title">Profile Perusahaan</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        @if ($dataCount == 0)
                            <a href="{{ route('kelola_data.profile_perusahaan.create') }}"
                                class="btn btn-success">Tambah</a>
                        @endif

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="profile_perusahaan" style="width: 100%">
                        <thead>
                            <tr>
                                <th class="text-center">Logo</th>
                                <th>Nama Toko</th>
                                <th>No WA</th>
                                <th>Alamat</th>
                                <th class="text-center">Sosmed</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media Modal -->
    <div class="modal fade" id="sosmedModal" tabindex="-1" aria-labelledby="sosmedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sosmedModalLabel">Social Media Links</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" class="form-control" id="ig" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" class="form-control" id="fb" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telegram</label>
                            <input type="text" class="form-control" id="telegram" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tokopedia</label>
                            <input type="text" class="form-control" id="tokopedia" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shoope</label>
                            <input type="text" class="form-control" id="shoope" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

            $('#profile_perusahaan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.profile_perusahaan.load') }}",
                    type: "POST",
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'logo',
                        render: function(data) {
                            return data ?
                                `<img src="{{ asset('storage/${data}') }}" class="logo-img" alt="Logo">` :
                                '<span class="text-muted">No Logo</span>';
                        },
                        className: 'text-center',
                        searchable: false // Add this since you're not searching by logo
                    },
                    {
                        data: 'nama_toko',
                        searchable: true
                    },
                    {
                        data: 'no_wa',
                        render: function(data) {
                            // Add +62 if the number doesn't already start with it
                            return data ? (data.startsWith('+62') ? data : '+62' + data) : '';
                        },
                        searchable: true
                    },
                    {
                        data: 'alamat',
                        searchable: true
                    },
                    {
                        data: 'sosmed',
                        render: function(data, type, row) {
                            return '<button class="btn btn-sm btn-info sosmed-btn" data-id="' + row
                                .id + '">Lihat</button>';
                        },
                        className: 'text-center',
                        searchable: false // Add this since you're not searching by sosmed
                    },
                    {
                        data: 'aksi',
                        className: 'text-center',
                        searchable: false,
                        orderable: false
                    }
                ],
                columnDefs: [{
                    className: "dt-head-center",
                    targets: ['_all']
                }]
            });

            // Handle social media button click
            $(document).on('click', '.sosmed-btn', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('kelola_data.profile_perusahaan.get_sosmed') }}",
                    type: "POST",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#ig').val(response.data.ig || '-');
                            $('#fb').val(response.data.fb || '-');
                            $('#telegram').val(response.data.telegram || '-');
                            $('#tokopedia').val(response.data.tokopedia || '-');
                            $('#shoope').val(response.data.shoope || '-');

                            var modal = new bootstrap.Modal(document.getElementById(
                                'sosmedModal'));
                            modal.show();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal memuat data social media', 'error');
                    }
                });
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
                                    $('#profile_perusahaan').DataTable().ajax.reload();
                                    location.reload();
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
