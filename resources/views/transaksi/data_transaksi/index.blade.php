@extends('template.app')
@section('title', 'Data Transaksi')
@section('content')
    <div class="page-heading">
        <h3>Data Transaksi</h3>
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
                        <h5 class="card-title">Data Transaksi</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="data_transaksi" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Nama Customer</th>
                                <th>No Telepon</th>
                                <th>Status Pembayaran</th>
                                <th>Total</th>
                                <th>Tanggal</th>
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

    <!-- Detail Transaction Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informasi Transaksi</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%">Kode Transaksi</td>
                                    <td id="modal-kode-transaksi"></td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td id="modal-tanggal"></td>
                                </tr>
                                <tr>
                                    <td>Customer</td>
                                    <td id="modal-customer"></td>
                                </tr>
                                <tr>
                                    <td>No. Telepon</td>
                                    <td id="modal-telepon"></td>
                                </tr>
                                <tr>
                                    <td>Status Pembayaran</td>
                                    <td id="modal-status"></td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td id="modal-total"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6>Detail Item</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="detailItemsTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detailItemsBody">
                                <!-- Items will be inserted here by JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment History Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6>Riwayat Pembayaran</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentHistoryTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentHistoryBody">
                                        <!-- Payment history will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="printInvoiceBtn" class="btn btn-info me-auto" target="_blank">
                        <i class="fas fa-print"></i> Cetak Invoice
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Print Option Modal -->
    <div class="modal fade" id="printOptionModal" tabindex="-1" aria-labelledby="printOptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printOptionModalLabel">Pilih Jenis Cetakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <a href="#" id="printInvoiceOption" class="btn btn-info">
                            <i class="fas fa-file-invoice"></i> Cetak Surat Jalan
                        </a>
                        <a href="#" id="printDeliveryNoteOption" class="btn btn-primary">
                            <i class="fas fa-truck"></i> Cetak Invoice
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).on('click', '.print-btn', function(e) {
            e.preventDefault();
            var transactionId = $(this).data('id');

            // Set the URLs for both print options
            $('#printInvoiceOption').attr('href', "{{ route('transaksi.data_transaksi.cetak_invoice', '') }}/" +
                transactionId);
            $('#printDeliveryNoteOption').attr('href',
                "{{ route('transaksi.data_transaksi.cetak_surat_jalan', '') }}/" + transactionId);

            // Show the print option modal
            $('#printOptionModal').modal('show');
        });

        // Tambahkan event handler untuk print options
        $(document).on('click', '#printInvoiceOption, #printDeliveryNoteOption', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            // Open in new window/tab for printing
            window.open(url, '_blank');

            // Close modal
            $('#printOptionModal').modal('hide');
        });

        // Tambahkan function untuk print langsung dari detail modal
        $(document).on('click', '#printInvoiceBtn', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            window.open(url, '_blank');
        });

        // Optional: Tambahkan shortcut keyboard untuk print
        $(document).keydown(function(e) {
            // Ctrl+P untuk print
            if (e.ctrlKey && e.keyCode == 80) {
                e.preventDefault();
                if ($('#printOptionModal').hasClass('show')) {
                    $('#printDeliveryNoteOption')[0].click();
                }
            }
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Add this to your existing JavaScript
            $(document).on('click', '.pay-btn', function(e) {
                e.preventDefault();
                var payUrl = $(this).data('url');
                var remainingAmount = $(this).data('remaining');

                Swal.fire({
                    title: 'Pembayaran Cicilan',
                    html: `
            <div class="mb-3">
                <label for="paymentAmount" class="form-label">Sisa Hutang: Rp ${parseFloat(remainingAmount).toLocaleString('id-ID')}</label>
                <input type="number" id="paymentAmount" class="form-control" placeholder="Masukkan jumlah pembayaran" min="1" max="${remainingAmount}">
            </div>
        `,
                    showCancelButton: true,
                    confirmButtonText: 'Bayar',
                    cancelButtonText: 'Batal',
                    focusConfirm: false,
                    preConfirm: () => {
                        const paymentAmount = Swal.getPopup().querySelector('#paymentAmount')
                            .value;
                        if (!paymentAmount || isNaN(paymentAmount)) {
                            Swal.showValidationMessage('Masukkan jumlah yang valid');
                        } else if (parseFloat(paymentAmount) <= 0) {
                            Swal.showValidationMessage('Jumlah harus lebih dari 0');
                        } else if (parseFloat(paymentAmount) > parseFloat(
                                remainingAmount)) {
                            Swal.showValidationMessage('Jumlah melebihi sisa hutang');
                        }
                        return {
                            paymentAmount: paymentAmount
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: payUrl,
                            type: 'POST',
                            data: {
                                payment_amount: result.value.paymentAmount
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    );
                                    $('#data_transaksi').DataTable().ajax.reload();
                                    if ($('#detailModal').hasClass('show')) {
                                        $('#detailModal').modal('hide');
                                    }
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                var errorMessage = xhr.responseJSON && xhr
                                    .responseJSON.message ?
                                    xhr.responseJSON.message :
                                    'Gagal memproses pembayaran';
                                Swal.fire(
                                    'Error!',
                                    errorMessage,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $('#data_transaksi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.data_transaksi.load') }}",
                    type: "POST",
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'kode_transaksi',
                        name: 'kode_transaksi'
                    },
                    {
                        data: 'nama_customer',
                        name: 'nama_customer'
                    },
                    {
                        data: 'no_hp_customer',
                        name: 'no_hp_customer'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        render: function(data, type, row) {
                            if (data === 'hutang') {
                                return '<span class="badge bg-warning text-dark">Hutang</span>';
                            } else if (data === 'lunas') {
                                return '<span class="badge bg-success">Lunas</span>';
                            } else {
                                return '<span class="badge bg-secondary">' + data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data, type, row) {
                            // Data is already formatted by the controller
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            return new Date(data).toLocaleDateString('id-ID');
                        }
                    },
                    {
                        data: 'aksi',
                        className: 'text-center text-nowrap',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    className: "dt-head-center",
                    targets: ['_all']
                }],
            });

            // Handle detail button click
            $(document).on('click', '.detail-btn', function(e) {
                e.preventDefault();
                var detailUrl = $(this).data('url');
                var paymentHistoryUrl = detailUrl.replace('/detail', '/payment-history');

                // Clear previous modal content
                $('#detailItemsBody').html(
                    '<tr><td colspan="6" class="text-center">Memuat data...</td></tr>');
                $('#paymentHistoryBody').html(
                    '<tr><td colspan="3" class="text-center">Memuat data...</td></tr>');
                $('.modal-footer').find('.pay-btn').remove();

                // Fetch detail data
                $.get(detailUrl, function(response) {
                    if (response.success) {
                        var transaksi = response.transaksi;
                        var items = response.items;

                        // Fill transaction info
                        $('#modal-kode-transaksi').text(transaksi.kode_transaksi);
                        $('#modal-tanggal').text(new Date(transaksi.created_at).toLocaleString(
                            'id-ID'));
                        $('#modal-customer').text(transaksi.nama_customer);
                        $('#modal-telepon').text(transaksi.no_hp_customer);
                        $('#modal-status').html(
                            transaksi.payment_status === 'hutang' ?
                            '<span class="badge bg-warning text-dark">Hutang</span>' :
                            (transaksi.payment_status === 'lunas' ?
                                '<span class="badge bg-success">Lunas</span>' :
                                '<span class="badge bg-secondary">' + transaksi
                                .payment_status +
                                '</span>')
                        );
                        $('#modal-total').html(
                            transaksi.payment_status === 'hutang' ?
                            'Total: Rp ' + parseFloat(transaksi.total).toLocaleString(
                                'id-ID') +
                            '<br>' +
                            'Dibayar: Rp ' + parseFloat(transaksi.paid_amount)
                            .toLocaleString(
                                'id-ID') + '<br>' +
                            'Sisa: Rp ' + parseFloat(transaksi.remaining_amount)
                            .toLocaleString(
                                'id-ID') :
                            'Total: Rp ' + parseFloat(transaksi.total).toLocaleString(
                                'id-ID')
                        );

                        $('#printInvoiceBtn').attr('href',
                            "{{ route('transaksi.data_transaksi.cetak_invoice', '') }}/" +
                            transaksi.id);
                        // Fill items table
                        var itemsHtml = '';
                        $.each(items, function(index, item) {
                            itemsHtml += '<tr>' +
                                '<td>' + (index + 1) + '</td>' +
                                '<td>' + item.kd_produk + '</td>' +
                                '<td>' + (item.judul || '-') + '</td>' +
                                '<td>' + item.quantity + '</td>' +
                                '<td>Rp ' + parseFloat(item.unit_price).toLocaleString(
                                    'id-ID') + '</td>' +
                                '<td>Rp ' + parseFloat(item.total_price).toLocaleString(
                                    'id-ID') + '</td>' +
                                '</tr>';
                        });
                        $('#detailItemsBody').html(itemsHtml);



                        // Load payment history
                        $.get(paymentHistoryUrl, function(historyResponse) {
                            if (historyResponse.success) {
                                var historyHtml = '';
                                if (historyResponse.history.length > 0) {
                                    $.each(historyResponse.history, function(index, item) {
                                        historyHtml += '<tr>' +
                                            '<td>' + (index + 1) + '</td>' +
                                            '<td>' + new Date(item.payment_date)
                                            .toLocaleString('id-ID') + '</td>' +
                                            '<td>Rp ' + parseFloat(item
                                                .payment_amount)
                                            .toLocaleString('id-ID') + '</td>' +
                                            '</tr>';
                                    });
                                } else {
                                    historyHtml =
                                        '<tr><td colspan="3" class="text-center">Tidak ada riwayat pembayaran</td></tr>';
                                }
                                $('#paymentHistoryBody').html(historyHtml);
                            } else {
                                $('#paymentHistoryBody').html(
                                    '<tr><td colspan="3" class="text-center">Gagal memuat riwayat pembayaran</td></tr>'
                                );
                            }
                        }).fail(function() {
                            $('#paymentHistoryBody').html(
                                '<tr><td colspan="3" class="text-center">Gagal memuat riwayat pembayaran</td></tr>'
                            );
                        });

                        // Show modal
                        $('#detailModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Gagal memuat detail transaksi', 'error');
                });
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: "Transaksi akan dihapus dan stok produk akan dikembalikan. Anda yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            dataType: 'json',
                            beforeSend: function() {
                                // Show loading indicator if needed
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Terhapus!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    // Refresh datatable
                                    $('#data_transaksi').DataTable().ajax.reload(null,
                                        false);
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message ||
                                        'Gagal menghapus transaksi',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat menghapus transaksi';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire(
                                    'Error!',
                                    errorMessage,
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
