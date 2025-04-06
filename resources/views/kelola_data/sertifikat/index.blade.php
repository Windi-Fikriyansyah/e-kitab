@extends('template.app')
@section('title', 'Master Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Master Sertifikat</h3>
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
                        <h5 class="card-title mb-0">Master Sertifikat</h5>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('kelola_data.sertifikat.create') }}" class="btn btn-success">Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="sertifikat">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Arsip Dokumen</th>
                                <th>Nomor Sertifikat</th>
                                <th>Tanggal</th>
                                <th>Kode Skpd</th>
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

            $('#sertifikat').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.sertifikat.load') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = data.search.value;
                    }
                },
                pageLength: 10,
                searching: true,
                columns: [
        {
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },
        {
            data: 'nomorRegister',
            name: 'nomorRegister'
        },
        {
            data: 'nomorSertifikat',
            name: 'nomorSertifikat'
        },
        {
            data: 'tanggalSertifikat',
            name: 'tanggalSertifikat'
        },
        {
            data: 'kodeSkpd',
            name: 'kodeSkpd'
        },
        {
            data: 'aksi',
            className: 'text-center'
        }
    ]
            });
        });

        function hapus(nomorRegister, kodeSkpd) {
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
                        url: "{{ route('kelola_data.sertifikat.delete') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            nomorRegister: nomorRegister,
                            kodeSkpd: kodeSkpd
                        },
                        success: function(response) {
                            swalWithBootstrapButtons.fire({
                                title: "Terhapus!",
                                text: response.message,
                                icon: "success"
                            });

                            let tabel = $('#sertifikat').DataTable();

                            tabel.ajax.reload();
                        },
                        error: function(e) {
                            swalWithBootstrapButtons.fire({
                                title: "Gagal!",
                                text: e.responseJSON.message,
                                icon: "error"
                            });
                        },
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
