@extends('template.app')
@section('title', 'Kelola Data BPKB')
@section('content')
    <div class="page-heading">
        <h3>Kelola Data History BPKB</h3>
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
                        <h5 class="card-title">History BPKB</h5>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="bpkb" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Arsip Dokumen</th>
                                <th>Nomor BPKB</th>
                                <th>Nomor Polisi</th>
                                <th>SKPD</th>
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
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#bpkb').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.historybpkb.load') }}",
                    type: "POST",
                    // data: function(data) {
                    //     data.search = $('input[type="search"]').val();
                    // }
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'nomorRegister',
                    }, {
                        data: 'nomorBpkb',
                    }, {
                        data: 'nomorPolisi',
                    }, {
                        data: 'kodeSkpd',
                    },
                    {
                        data: 'aksi',
                        className: 'text-center'
                    }
                ],
                columnDefs: [
                    // Center align both header and body content of columns 1, 2 & 3
                    {
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 4]
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
                        url: "{{ route('kelola_data.bpkb.delete') }}",
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

                            let tabel = $('#bpkb').DataTable();

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
