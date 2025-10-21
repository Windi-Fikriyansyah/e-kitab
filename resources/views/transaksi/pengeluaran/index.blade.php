@extends('template.app')
@section('title', 'Pengeluaran')
@section('content')
    <div class="page-heading">
        <h3>Data Pengeluaran</h3>
    </div>

    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card radius-10">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Pengeluaran</h5>
                <a href="{{ route('transaksi.pengeluaran.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Pengeluaran
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="data_pengeluaran" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th>Nominal</th>
                                <th>Metode Bayar</th>
                                <th>Lampiran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#data_pengeluaran').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.pengeluaran.load') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'nominal',
                        name: 'nominal',
                        render: function(data) {
                            return 'Rp ' + parseFloat(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: 'metode_bayar',
                        name: 'metode_bayar'
                    },
                    {
                        data: 'lampiran',
                        name: 'lampiran',
                        render: function(data) {
                            return data ?
                                `<a href="/storage/${data}" target="_blank" class="btn btn-sm btn-info">Lihat</a>` :
                                '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });


        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Siapkan CSRF untuk semua AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });

            // Event delegation: berlaku untuk row dinamis DataTables
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const table = $('#data_pengeluaran').DataTable();

                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Data pengeluaran akan dihapus permanen. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    // Coba request DELETE (standar Laravel)
                    $.ajax({
                            url: url,
                            type: 'DELETE',
                            dataType: 'json'
                        })
                        .done(function(res) {
                            if (res && res.success) {
                                Swal.fire('Terhapus!', res.message || 'Data berhasil dihapus.',
                                    'success');
                                table.ajax.reload(null, false); // reload tanpa reset halaman
                            } else {
                                Swal.fire('Gagal', (res && res.message) ? res.message :
                                    'Gagal menghapus data.', 'error');
                            }
                        })
                        .fail(function(xhr) {
                            // Fallback kalau server tidak mengizinkan DELETE langsung (mis. mod_security)
                            if (xhr.status === 405 || xhr.status === 419) {
                                $.post(url, {
                                        _method: 'DELETE'
                                    })
                                    .done(function(res) {
                                        if (res && res.success) {
                                            Swal.fire('Terhapus!', res.message ||
                                                'Data berhasil dihapus.', 'success');
                                            table.ajax.reload(null, false);
                                        } else {
                                            Swal.fire('Gagal', (res && res.message) ? res
                                                .message : 'Gagal menghapus data.',
                                                'error');
                                        }
                                    })
                                    .fail(function(xhr2) {
                                        const msg = (xhr2.responseJSON && xhr2.responseJSON
                                                .message) ? xhr2.responseJSON.message :
                                            'Terjadi kesalahan.';
                                        Swal.fire('Error', msg, 'error');
                                    });
                            } else {
                                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                                    .responseJSON.message : 'Terjadi kesalahan.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                });
            });
        });
    </script>
@endpush
