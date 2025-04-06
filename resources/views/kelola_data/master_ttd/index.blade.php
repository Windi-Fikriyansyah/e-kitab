@extends('template.app')
@section('title', 'Master TTD')
@section('content')
    <div class="page-heading">
        <h3>Master TTD</h3>
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
                        <h5 class="card-title mb-0">Master TTD</h5>
                        <div class="dropdown ms-auto">
                            <a href="{{ route('kelola_data.master_ttd.create') }}" class="btn btn-success">Tambah</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="master_ttd">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Pangkat</th>
                                <th>Kd SKPD</th>
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

            $('#master_ttd').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.master_ttd.load') }}",
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
            data: 'nip',
            name: 'nip'
        },
        {
            data: 'nama',
            name: 'nama'
        },
        {
            data: 'jabatan',
            name: 'jabatan'
        },
        {
            data: 'pangkat',
            name: 'pangkat'
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

        function hapus(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/kelola_data/master_ttd/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            response.message,
                            'success'
                        );
                        $('#master_ttd').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'Failed!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the item.',
                        'error'
                    );
                }
            });
        }
    });
}
    </script>

@endpush
