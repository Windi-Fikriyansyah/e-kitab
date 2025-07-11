@extends('template.app')
@section('title', 'Laporan Stok')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="stokTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="stok-tersedia-tab" data-bs-toggle="tab"
                                data-bs-target="#stok-tersedia" type="button" role="tab"
                                onclick="loadDataTable('tersedia')">
                                Stok Tersedia
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="stok-minimum-tab" data-bs-toggle="tab"
                                data-bs-target="#stok-minimum" type="button" role="tab"
                                onclick="loadDataTable('minimum')">
                                Stok Minimum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mutasi-stok-tab" data-bs-toggle="tab" data-bs-target="#mutasi-stok"
                                type="button" role="tab" onclick="loadDataTable('mutasi')">
                                Mutasi Stok
                            </button>
                        </li>
                    </ul>

                    <button type="button" class="btn btn-success mt-3" onclick="exportExcel()">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="stokTabContent">
                <!-- Tab Stok Tersedia -->
                <div class="tab-pane fade show active" id="stok-tersedia" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="stokTersediaTable" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                    <th>Stok Minimum</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Stok Minimum -->
                <div class="tab-pane fade" id="stok-minimum" role="tabpanel">
                    <div class="alert alert-warning">
                        Menampilkan barang dengan stok di bawah stok minimum
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="stokMinimumTable" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                    <th>Stok Minimum</th>
                                    <th>Kategori</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Mutasi Stok -->
                <div class="tab-pane fade" id="mutasi-stok" role="tabpanel">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white mb-3">
                            <i class="fas fa-filter me-2"></i> Filter Mutasi Stok
                        </div>
                        <div class="card-body">
                            <form id="mutasiFilterForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="tanggal_awal_mutasi" class="form-label">Tanggal Awal</label>
                                        <input type="date" class="form-control" id="tanggal_awal_mutasi"
                                            name="tanggal_awal" value="{{ $tanggalAwal ?? date('Y-m-01') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tanggal_akhir_mutasi" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="tanggal_akhir_mutasi"
                                            name="tanggal_akhir" value="{{ $tanggalAkhir ?? date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="barang_id_mutasi" class="form-label">Barang</label>
                                        <select class="form-select" id="barang_id_mutasi" name="barang_id">
                                            <option value="">Semua Barang</option>
                                            @foreach ($barangList as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ isset($barangId) && $barangId == $item->id ? 'selected' : '' }}>
                                                    {{ $item->kd_produk }} - {{ $item->judul }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="loadMutasiStok()">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="mutasiStokTable" style="width:100%">
                            <thead class="table-info">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .table th {
            white-space: nowrap;
        }
    </style>

@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let stokTersediaTable, stokMinimumTable, mutasiStokTable;
        let currentTab = 'tersedia';

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize all tables but only load data for the active tab
            initTables();
            loadDataTable(currentTab);

            // Aktifkan tab berdasarkan hash URL
            if (window.location.hash) {
                const tabTrigger = new bootstrap.Tab(document.querySelector(
                    `[data-bs-target="${window.location.hash}"]`
                ));
                tabTrigger.show();
                currentTab = window.location.hash.replace('#', '').replace('stok-', '').replace('-', '');
                loadDataTable(currentTab);
            }

            // Update hash saat tab berubah
            document.querySelectorAll('#stokTabs .nav-link').forEach(tab => {
                tab.addEventListener('click', function() {
                    window.location.hash = this.getAttribute('data-bs-target');
                    currentTab = this.getAttribute('data-bs-target').replace('#', '').replace(
                        'stok-', '').replace('-', '');
                });
            });
        });

        function initTables() {
            // Inisialisasi tabel stok tersedia
            stokTersediaTable = $('#stokTersediaTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('laporan.laporan-stok.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.type = 'tersedia';
                    },
                    error: function(xhr, error, thrown) {
                        console.log('Error:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'kd_produk',
                        name: 'kd_produk'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'stok',
                        name: 'stok'
                    },
                    {
                        data: 'stok_minimum',
                        name: 'stok_minimum'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // Inisialisasi tabel stok minimum
            stokMinimumTable = $('#stokMinimumTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('laporan.laporan-stok.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.type = 'minimum';
                    },
                    error: function(xhr, error, thrown) {
                        console.log('Error:', xhr.responseText);
                    }
                },
                columns: [{
                        data: 'kd_produk',
                        name: 'kd_produk'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'stok',
                        name: 'stok'
                    },
                    {
                        data: 'stok_minimum',
                        name: 'stok_minimum'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'selisih',
                        name: 'selisih',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [5, 'desc']
                ]
            });

            // Inisialisasi tabel mutasi stok
            mutasiStokTable = $('#mutasiStokTable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('laporan.laporan-stok.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.type = 'mutasi';
                        d.tanggal_awal = $('#tanggal_awal_mutasi').val();
                        d.tanggal_akhir = $('#tanggal_akhir_mutasi').val();
                        d.barang_id = $('#barang_id_mutasi').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.log('Error:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat data mutasi stok'
                        });
                    }
                },
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'kd_produk',
                        name: 'kd_produk'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'jenis_badge',
                        name: 'jenis',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jumlah',
                        name: 'jumlah'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
        }

        function loadDataTable(type) {
            currentTab = type;
            switch (type) {
                case 'tersedia':
                    stokTersediaTable.ajax.reload();
                    break;
                case 'minimum':
                    stokMinimumTable.ajax.reload();
                    break;
                case 'mutasi':
                    loadMutasiStok();
                    break;
            }
        }

        function loadMutasiStok() {
            // Validasi tanggal
            const tanggalAwal = $('#tanggal_awal_mutasi').val();
            const tanggalAkhir = $('#tanggal_akhir_mutasi').val();

            if (!tanggalAwal || !tanggalAkhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Tanggal awal dan akhir harus diisi'
                });
                return;
            }

            if (new Date(tanggalAwal) > new Date(tanggalAkhir)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir'
                });
                return;
            }

            mutasiStokTable.ajax.reload();
        }

        function exportExcel() {
            let url = '';
            let params = '';

            switch (currentTab) {
                case 'tersedia':
                    url = "{{ route('laporan.laporan-stok.export') }}";
                    params = "?type=tersedia";
                    break;
                case 'minimum':
                    url = "{{ route('laporan.laporan-stok.export') }}";
                    params = "?type=minimum";
                    break;
                case 'mutasi':
                    const tanggalAwal = $('#tanggal_awal_mutasi').val();
                    const tanggalAkhir = $('#tanggal_akhir_mutasi').val();

                    if (!tanggalAwal || !tanggalAkhir) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: 'Tanggal awal dan akhir harus diisi untuk export'
                        });
                        return;
                    }

                    url = "{{ route('laporan.laporan-stok.export') }}";
                    params = "?type=mutasi" +
                        "&tanggal_awal=" + tanggalAwal +
                        "&tanggal_akhir=" + tanggalAkhir +
                        "&barang_id=" + ($('#barang_id_mutasi').val() || '');
                    break;
            }

            if (url && params) {
                window.location.href = url + params;
            }
        }

        // Event listener untuk filter form
        $('#mutasiFilterForm').on('submit', function(e) {
            e.preventDefault();
            loadMutasiStok();
        });

        // Event listener untuk perubahan tanggal
        $('#tanggal_awal_mutasi, #tanggal_akhir_mutasi').on('change', function() {
            if (currentTab === 'mutasi') {
                loadMutasiStok();
            }
        });
    </script>
@endpush
