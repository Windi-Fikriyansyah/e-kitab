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

        <div class="modal fade" id="addColumnModal" tabindex="-1" aria-labelledby="addColumnModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addColumnModalLabel">Tambah Kolom Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addColumnForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="column_name" class="form-label">Nama Kolom</label>
                                <input type="text" class="form-control" id="column_name" name="column_name" required>
                                <small class="text-muted">Gunakan format snake_case (contoh: nama_kolom)</small>
                            </div>
                            <div class="mb-3">
                                <label for="data_type" class="form-label">Tipe Data</label>
                                <select class="form-select" id="data_type" name="data_type" required>
                                    <option value="string">String (Text Pendek)</option>
                                    <option value="text">Text (Panjang)</option>
                                    <option value="integer">Integer (Angka)</option>
                                    <option value="decimal">Decimal (Desimal)</option>
                                    <option value="boolean">Boolean (True/False)</option>
                                    <option value="date">Date (Tanggal)</option>
                                    <option value="datetime">DateTime (Tanggal & Waktu)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="barcodeModalLabel">Barcode Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="barcodeContainer"></div>
                        <p id="barcodeText" class="mt-2"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-success" onclick="downloadBarcodeAsJPG()">Download
                            JPG</button>
                        <button type="button" class="btn btn-primary" onclick="printBarcode()">Cetak</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">Data Produk</h5>
                    </div>
                    <div class="dropdown ms-auto">
                        <!-- Export PDF -->
                        {{-- <a id="exportPdfBtn" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a> --}}
                        <!-- Tombol Import -->
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fas fa-file-import"></i> Import Excel
                        </button>


                        <!-- Export Excel -->
                        <a id="exportExcelBtn" class="btn btn-success me-2">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>

                        <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                            <i class="fas fa-plus"></i> Tambah Kolom
                        </button>
                        <a href="{{ route('kelola_data.produk.create') }}" class="btn btn-success">Tambah Produk</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <input type="text" class="form-control filter-input" placeholder="Filter Kd Produk"
                            data-column="kd_produk">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control filter-input" placeholder="Filter Judul"
                            data-column="judul">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select select2-penulis" data-column="penulis">
                            <option value="">Semua Penulis</option>
                            @foreach ($penulis as $penulis)
                                <option value="{{ $penulis->nama_arab }}">{{ $penulis->nama_arab }} |
                                    {{ $penulis->nama_indonesia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select select2-kategori" data-column="kategori">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->nama_arab }}">{{ $kategori->nama_arab }} |
                                    {{ $kategori->nama_indonesia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select select2-penerbit" data-column="penerbit">
                            <option value="">Semua Penerbit</option>
                            @foreach ($penerbits as $penerbit)
                                <option value="{{ $penerbit->nama_arab }}">{{ $penerbit->nama_arab }} |
                                    {{ $penerbit->nama_indonesia }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select filter-select select2-supplier" data-column="supplier">
                            <option value="">Semua Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama_supplier }} |
                                    {{ $supplier->telepon }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-2">
                        <input type="text" class="form-control filter-input" placeholder="Filter Stok"
                            data-column="stok">
                    </div>
                </div>
                <div class="table-responsive">
                    <div class="row mb-3 g-2">

                        <!-- Card Total Stok -->
                        <div class="col-md-2 col-6">
                            <div class="card shadow-sm border-0" style="background:#ffffff;">
                                <div class="card-body p-2">
                                    <h6 class="mb-1 text-muted" style="font-size:12px;">Total Stok</h6>
                                    <h5 id="totalStok" class="fw-bold text-dark m-0" style="font-size:18px;"></h5>
                                </div>
                            </div>
                        </div>

                        <!-- Card Total Harga Jual (lebih lebar) -->
                        <div class="col-md-3 col-6">
                            <div class="card shadow-sm border-0" style="background:#ffffff;">
                                <div class="card-body p-2">
                                    <h6 class="mb-1 text-muted" style="font-size:12px;">Total Harga Jual</h6>
                                    <h5 id="totalHarga" class="fw-bold text-dark m-0" style="font-size:18px;"></h5>
                                </div>
                            </div>
                        </div>

                    </div>



                    <table class="table align-middle mb-0" id="produk" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th style="display:none;">Kd Produk</th>
                                <th>Judul</th>
                                <th style="display:none;">Penulis</th>
                                <th style="display:none;">Kategori</th>
                                <th>Penerbit</th>
                                <th style="display:none;">Supplier</th>
                                <th style="display:none;">Harga Modal</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Supplier</th>
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


    <!-- Modal Import Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('kelola_data.produk.importExcel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel (.xlsx)</label>
                            <input type="file" name="file" class="form-control" required accept=".xlsx">
                            <small class="text-muted">Format harus .xlsx</small>
                        </div>

                        <a href="{{ route('kelola_data.produk.downloadTemplate') }}" class="btn btn-sm btn-link">
                            Download Template Excel
                        </a>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Container tombol aksi */
        .aksi-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: center;
        }

        /* Tombol ukuran kecil agar muat di HP */
        .btn-action-xs {
            padding: 4px 6px !important;
            font-size: 11px !important;
        }

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

        #barcodeContainer svg {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        // Ketika klik export PDF
        $('#exportPdfBtn').click(function() {
            let params = collectFilters();
            window.open("{{ route('kelola_data.produk.exportPdf') }}?" + params, "_blank");
        });

        // Ketika klik export Excel
        $('#exportExcelBtn').click(function() {
            let params = collectFilters();
            window.location.href = "{{ route('kelola_data.produk.exportExcelProduk') }}?" + params;
        });

        // Fungsi untuk mengambil seluruh filter
        function collectFilters() {
            let params = [];

            $('.filter-input, .filter-select').each(function() {
                if ($(this).val() !== "") {
                    params.push($(this).data("column") + "=" + encodeURIComponent($(this).val()));
                }
            });

            return params.join("&");
        }


        window.printBarcode = function() {
            // Clone the barcode container for printing
            const printContent = document.getElementById('barcodeContainer').cloneNode(true);
            const printWindow = window.open('', '', 'width=600,height=600');

            printWindow.document.open();
            printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak Barcode</title>
            <style>
                body {
                    text-align: center;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                svg {
                    margin: 20px auto;
                    display: block;
                }
                @media print {
                    body { padding: 0; }
                    button { display: none; }
                }
            </style>
        </head>
        <body>
            <h3>Barcode Produk</h3>
            <div id="printContent">${printContent.innerHTML}</div>
            <p>${document.getElementById('barcodeText').textContent}</p>
            <button onclick="window.print()">Cetak</button>
            <button onclick="downloadBarcodeImage()">Download JPG</button>
            <button onclick="window.close()">Tutup</button>
            <script>
                function downloadBarcodeImage() {
                    html2canvas(document.querySelector("#printContent")).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'barcode.jpg';
                        link.href = canvas.toDataURL('image/jpeg');
                        link.click();
                    });
                }
            <\/script>
        </body>
        </html>
    `);
            printWindow.document.close();
        };

        // Tambahkan fungsi untuk download JPG langsung dari modal
        function downloadBarcodeAsJPG() {
            html2canvas(document.querySelector("#barcodeContainer")).then(canvas => {
                const link = document.createElement('a');
                link.download = 'barcode_' + document.getElementById('barcodeText').textContent.replace(
                    /[^a-z0-9]/gi, '_').toLowerCase() + '.jpg';
                link.href = canvas.toDataURL('image/jpeg');
                link.click();
            });
        }


        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi Select2
            $('.select2-penulis').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Penulis",
                allowClear: true
            });
            $('.select2-kategori').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Kategori",
                allowClear: true
            });

            $('.select2-penerbit').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Penerbit",
                allowClear: true
            });

            $('.select2-supplier').select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Supplier",
                allowClear: true
            });

            var table = $('#produk').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kelola_data.produk.load') }}",
                    type: "POST",
                    data: function(d) {
                        // Tambahkan parameter filter untuk setiap kolom
                        $('.filter-input, .filter-select').each(function() {
                            if ($(this).val() != '') {
                                d.columns.find(col => col.data === $(this).data('column'))
                                    .search.value = $(this).val();
                            }
                        });
                    }
                },
                pageLength: 10,
                searching: true,
                scrollX: true,
                columns: [{
                        data: 'images',
                        name: 'images',
                        render: function(data) {
                            if (data) {
                                try {
                                    const images = JSON.parse(data);
                                    if (images.length > 0) {
                                        // Ambil gambar pertama
                                        const imageUrl = "{{ asset('storage/products') }}/" +
                                            images[0];
                                        return `<img src="${imageUrl}" class="product-thumbnail" alt="Product Image">`;
                                    }
                                } catch (e) {
                                    console.error('Error parsing images:', e);
                                }
                            }
                            return '<img src="{{ asset('assets/images/no-image.png') }}" class="product-thumbnail" alt="No Image">';
                        },
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'kd_produk',
                        visible: false,
                    }, {
                        data: 'judul',
                    }, {
                        data: 'penulis',
                        visible: false
                    }, {
                        data: 'kategori',
                        visible: false
                    }, {
                        data: 'penerbit',
                    }, {
                        data: 'supplier',
                        visible: false
                    }, {
                        data: 'harga_modal',
                        visible: false,
                        render: function(data) {
                            if (!data) return '-';
                            return data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        }
                    }, {
                        data: 'harga_jual',
                        render: function(data) {
                            if (!data) return '-';
                            return data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        }
                    },
                    {
                        data: 'stok',
                    },
                    {
                        data: 'supplier',
                    },
                    {
                        data: 'aksi',
                        className: 'text-center',
                        width: "120px"
                    }
                ],
                drawCallback: function(settings) {
                    let api = this.api();

                    let totalStok = settings.json.total_stok ?? 0;
                    let totalHarga = settings.json.total_harga ?? 0;

                    $('#totalStok').text(totalStok.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                    $('#totalHarga').text(totalHarga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                },
                columnDefs: [{
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 7, 8, 10]
                    }
                ]
            });

            // Event listener untuk filter input
            $('.filter-input').keyup(function() {
                table.ajax.reload();
            });

            // Event listener untuk filter select
            $('.filter-select').change(function() {
                table.ajax.reload();
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
                                    table.ajax.reload();
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

            $(document).on('click', '.barcode-btn', function() {
                var kdProduk = $(this).data('kd');
                var judulProduk = $(this).data('judul');

                $('#barcodeText').text(kdProduk + ' - ' + judulProduk);
                $('#barcodeContainer').html('<svg id="barcode"></svg>');

                // Generate barcode
                JsBarcode("#barcode", kdProduk, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 50,
                    displayValue: false
                });

                $('#barcodeModal').modal('show');
            });


            $('#addColumnForm').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Kolom baru akan ditambahkan ke database",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, tambahkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kelola_data.produk.addColumn') }}",
                            type: "POST",
                            data: $(this).serialize(),
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    );
                                    $('#addColumnModal').modal('hide');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON.message ||
                                    'Gagal menambahkan kolom',
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
