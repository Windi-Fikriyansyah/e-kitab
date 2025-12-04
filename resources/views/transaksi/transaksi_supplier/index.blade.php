@extends('template.app')
@section('title', 'Data Transaksi Supplier')
@section('content')
    <div class="page-heading">
        <h3>Data Transaksi Supplier</h3>
    </div>
    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif




        <div class="card radius-10">

            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Filter Supplier</label>
                        <select id="filter_supplier" class="form-select">
                            <option value="">-- Semua Supplier --</option>
                            @foreach ($supplier as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <a href="#" id="btnExportPDF" class="btn btn-danger">
                            <i class="bx bx-file"></i> Export PDF
                        </a>
                    </div>
                    <div class="mb-3 d-flex justify-content-end">
                        <a href="{{ route('transaksi.transaksi_supplier.create') }}" class="btn btn-success">
                            <i class="bx bx-plus"></i> Buat Transaksi Supplier
                        </a>
                    </div>
                </div>




                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="produk" style="width: 100%">
                        <thead>
                            <tr>

                                <th>Kode Transaksi</th>
                                <th>Supplier</th>
                                <th>Total Tagihan</th>
                                <th>Resi</th>
                                <th>Fee</th>
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


    <!-- MODAL DETAIL TRANSAKSI -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Transaksi</th>
                            <td id="d_kode"></td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td id="d_supplier"></td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td id="d_total"></td>
                        </tr>
                        <tr>
                            <th>Resi</th>
                            <td id="d_resi"></td>
                        </tr>
                        <tr>
                            <th>Fee</th>
                            <td id="d_fee"></td>
                        </tr>
                        <tr>
                            <th>Total Tagihan</th>
                            <td id="d_total_tagihan"></td>
                        </tr>
                    </table>

                    <h5 class="mt-3">Produk</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="detailProduk"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Grand Total:</th>
                                <th id="grandTotal"></th> <!-- BARU -->
                            </tr>
                        </tfoot>
                    </table>

                </div>

            </div>
        </div>
    </div>

@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
        function formatRupiah(angka) {
            if (angka === null || angka === "" || isNaN(angka)) return "Rp 0";
            return "Rp " + parseInt(angka).toLocaleString("id-ID");
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#produk').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.transaksi_supplier.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.filter_supplier = $('#filter_supplier').val();
                    }
                },
                pageLength: 10,
                searching: true,
                scrollX: true,
                columns: [{
                        data: 'kode_transaksi'
                    },
                    {
                        data: 'nama_supplier'
                    },

                    // FORMAT TOTAL
                    {
                        data: 'total_tagihan',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },

                    // FORMAT RESI
                    {
                        data: 'resi',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },

                    // FORMAT FEE
                    {
                        data: 'fee',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },

                    {
                        data: 'aksi',
                        className: 'text-center text-nowrap',
                        width: "180px"
                    }
                ],

                columnDefs: [{
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 3, 4]
                    }
                ]
            });


            $('#filter_supplier').change(function() {
                table.ajax.reload();
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

            $(document).on('click', '.detail-btn', function() {
                let id = $(this).data('id');

                $.get("{{ url('/transaksi/transaksi_supplier/detail') }}/" + id, function(res) {

                    // HEADER
                    $("#d_kode").text(res.transaksi.kode_transaksi);
                    $("#d_supplier").text(res.transaksi.nama_supplier);
                    $("#d_total").text(formatRupiah(res.transaksi.total));
                    $("#d_resi").text(formatRupiah(res.transaksi.resi));
                    $("#d_fee").text(formatRupiah(res.transaksi.fee));
                    $("#d_total_tagihan").text(formatRupiah(res.transaksi.total_tagihan));

                    // DETAIL PRODUK
                    let html = "";
                    let grandTotal = 0;
                    res.detail.forEach(d => {

                        let total = d.harga * d.qty;
                        grandTotal += total;
                        html += `
                <tr>
                    <td>${d.judul}</td>
                    <td>${formatRupiah(d.harga)}</td>
                    <td>${d.qty}</td>
                    <td>${formatRupiah(total)}</td>
                </tr>
            `;
                    });

                    $("#detailProduk").html(html);

                    $("#grandTotal").text(formatRupiah(grandTotal));
                    // Tampilkan modal
                    $("#detailModal").modal('show');
                });
            });



            $('#btnExportPDF').click(function() {
                let supplier = $('#filter_supplier').val();
                window.open("{{ route('transaksi.transaksi_supplier.exportPDF') }}?supplier=" + supplier,
                    "_blank");
            });

        });
    </script>
@endpush
