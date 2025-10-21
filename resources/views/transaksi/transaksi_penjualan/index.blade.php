<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Kitab</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css"
        integrity="sha512-z/90a5SWiu4MWVelb5+ny7sAayYUfMmdXKEAbpj27PfdkamNdyI3hcjxPxkOPbrXoKIm7r9V2mElt5f1OtVhqA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        #diskon-input-container {
            transition: all 0.3s ease;
        }

        #diskon-persen {
            width: 100px;
            display: inline-block;
        }

        .card-widget {
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .chosen-select {
            width: calc(100% - 40px);
            display: inline-block;
        }

        .payment-option {
            margin-right: 15px;
        }

        .payment-option input[type="radio"] {
            margin-right: 5px;
        }

        .toast-top-right {
            top: 20px;
            right: 20px;
        }

        .toast-success {
            background-color: #28a745;
        }

        .toast-error {
            background-color: #dc3545;
        }

        .toast-info {
            background-color: #17a2b8;
        }

        .toast-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .dropship-mode .custom-price-column {
            display: table-cell;
        }

        .custom-price-column {
            display: none;
        }

        .custom-price-input {
            display: none;
        }

        .dropship-mode .custom-price-input {
            display: inline-block;
        }

        /* Sembunyikan kolom harga custom secara default */
        .custom-price-header,
        .custom-price-cell {
            display: none;
        }

        /* Tampilkan kolom harga custom saat mode dropship aktif */
        .dropship-mode .custom-price-header,
        .dropship-mode .custom-price-cell {
            display: table-cell;
        }

        /* Atur lebar kolom */
        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 5%;
        }

        /* No */
        .table th:nth-child(2),
        .table td:nth-child(2) {
            width: 10%;
        }

        /* Kode Produk */
        .table th:nth-child(3),
        .table td:nth-child(3) {
            width: 25%;
        }

        /* Judul */
        .table th:nth-child(4),
        .table td:nth-child(4) {
            width: 15%;
        }

        /* Supplier */
        .table th:nth-child(5),
        .table td:nth-child(5) {
            width: 10%;
        }

        /* Harga */
        .table th:nth-child(6),
        .table td:nth-child(6) {
            width: 12%;
        }

        /* Harga Custom */
        .table th:nth-child(7),
        .table td:nth-child(7) {
            width: 8%;
        }

        /* Qty */
        .table th:nth-child(8),
        .table td:nth-child(8) {
            width: 10%;
        }

        /* Subtotal */
        .table th:nth-child(9),
        .table td:nth-child(9) {
            width: 5%;
        }

        #potongan-input-container {
            transition: all 0.3s ease;
        }

        #potongan-nominal {
            width: 150px;
            display: inline-block;
        }

        /* Dropship info styles */
        .dropship-info {
            display: none;
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .dropship-mode .dropship-info {
            display: block;
        }

        .dropship-section {
            margin-bottom: 15px;
        }

        .dropship-section h5 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        #ekspedisi-logo-preview {
            max-width: 100px;
            max-height: 50px;
            margin-top: 10px;
            display: none;
        }

        /* Aksi */
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row align-items-center mt-4">
            <div class="col-md-8 col-12 mb-2">
                <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#modal-produk">
                    <i class="fa fa-plus mr-2"></i>Pilih Produk
                </button>

                <div class="form-check form-check-inline mt-2 mt-md-0">
                    <input class="form-check-input" type="checkbox" id="dropship-mode" name="dropship_mode">
                    <label class="form-check-label" for="dropship-mode">Mode Dropship</label>
                </div>
            </div>

            <div class="col-md-4 col-12 text-md-right text-left mb-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-danger">
                    <i class="fa fa-home mr-2"></i>Home
                </a>
            </div>
        </div>
        <form id="form-transaksi" action="{{ route('transaksi.transaksi_penjualan.simpan') }}" method="post"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-widget">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Judul</th>
                                        <th>Supplier</th>
                                        <th>Harga</th>
                                        <th class="custom-price-header">Harga Custom</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart_table">
                                    <!-- Cart items will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-widget">
                        <div class="card-body">
                            <table width="100%">
                                <tr>
                                    <td style="vertical-align:top">
                                        <label>Status Pembayaran</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="payment-option">
                                                <input type="radio" id="lunas" name="status_pembayaran"
                                                    value="lunas" checked>
                                                <label for="lunas">Lunas</label>
                                            </div>
                                            <div class="payment-option">
                                                <input type="radio" id="hutang" name="status_pembayaran"
                                                    value="hutang">
                                                <label for="hutang">Hutang</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top">
                                        <label>Metode Pembayaran</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div id="metode-lunas">
                                                <div class="payment-option">
                                                    <input type="radio" id="tunai" name="metode_pembayaran"
                                                        value="tunai" checked>
                                                    <label for="tunai">Tunai</label>
                                                </div>
                                                <div class="payment-option">
                                                    <input type="radio" id="transfer" name="metode_pembayaran"
                                                        value="transfer">
                                                    <label for="transfer">Transfer</label>
                                                </div>
                                                <div class="payment-option">
                                                    <input type="radio" id="qris" name="metode_pembayaran"
                                                        value="qris">
                                                    <label for="qris">QRIS</label>
                                                </div>
                                            </div>
                                            <div id="metode-hutang" style="display: none;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="dp"
                                                        name="metode_pembayaran" value="dp">
                                                    <label class="form-check-label" for="dp">DP</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="cod"
                                                        name="metode_pembayaran" value="cod">
                                                    <label class="form-check-label" for="cod">COD</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top">
                                        <label for="channel_order">Channel Order</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control" name="channel_order" id="channel_order"
                                                required>
                                                <option value="">Pilih Channel</option>
                                                <option value="Offline">Offline</option>
                                                <option value="WA">WA</option>
                                                <option value="Marketplace">Marketplace</option>
                                                <option value="Supplier Fulfillment">Supplier Fulfillment</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="ekspedisi">Ekspedisi</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control" name="ekspedisi" id="ekspedisi">
                                                <option value="">Pilih Ekspedisi</option>
                                                <!-- Opsi ekspedisi akan diisi melalui JavaScript -->
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="ekspedisi-lain-container" style="display:none;">
                                    <td style="vertical-align:top; width:30%">
                                        <label for="ekspedisi_lain">Nama Ekspedisi</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="ekspedisi_lain"
                                                id="ekspedisi_lain">
                                        </div>
                                    </td>
                                </tr>
                                <tr id="ekspedisi-logo-container" style="display:none;">
                                    <td style="vertical-align:top; width:30%">
                                        <label for="ekspedisi_logo">Logo Ekspedisi</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="file" class="form-control-file" name="ekspedisi_logo"
                                                id="ekspedisi_logo" accept="image/*">
                                            <small class="text-muted">Upload logo ekspedisi (format: jpg, png, max
                                                2MB)</small>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-widget">
                        <div class="card-body">
                            <table width="100%">
                                <tr>
                                    <td style="vertical-align:top; width:20%">
                                        <label for="user">Kasir</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="user" id="id_user"
                                                value="{{ auth()->user()->name }}" class="form-control" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <!-- In the deposit input section (around line 340) -->
                                <tr>
                                    <td style="vertical-align:top; width:20%">
                                        <label for="deposit">Deposit Tersedia</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="deposit" id="deposit" value="0"
                                                class="form-control" readonly>
                                            <small class="text-muted">Klik "Gunakan Deposit" untuk menggunakan</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <button type="button" id="use-deposit-btn" class="btn btn-primary btn-sm">
                                            <i class="fas fa-coins"></i> Gunakan Deposit
                                        </button>
                                        <button type="button" id="reset-deposit-btn"
                                            class="btn btn-danger btn-sm ml-2">
                                            <i class="fas fa-times"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:20%">
                                        <label for="pelanggan">Pelanggan</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <select
                                                    class="form-control select2 @error('customer') is-invalid @enderror"
                                                    name="customer" id="customer" style="width: 100%;">
                                                    <option value="">Pilih Pelanggan</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" data-toggle="modal"
                                                        data-target="#modal-pelanggan" class="btn btn-primary">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('customer')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Dropship Information Section -->
                            <div class="dropship-info">
                                <div class="dropship-section">
                                    <h5>Informasi Pengirim</h5>
                                    <div class="form-group">
                                        <label for="nama_pengirim">Nama Pengirim</label>
                                        <input type="text" class="form-control" name="nama_pengirim"
                                            id="nama_pengirim">
                                    </div>
                                    <div class="form-group">
                                        <label for="telepon_pengirim">No. Telepon Pengirim</label>
                                        <input type="text" class="form-control" name="telepon_pengirim"
                                            id="telepon_pengirim">
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat_pengirim">Alamat Pengirim</label>
                                        <textarea class="form-control" name="alamat_pengirim" id="alamat_pengirim" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="dropship-section">
                                    <h5>Informasi Penerima</h5>
                                    <div class="form-group">
                                        <label for="nama_penerima">Nama Penerima</label>
                                        <input type="text" class="form-control" name="nama_penerima"
                                            id="nama_penerima">
                                    </div>
                                    <div class="form-group">
                                        <label for="telepon_penerima">No. Telepon Penerima</label>
                                        <input type="text" class="form-control" name="telepon_penerima"
                                            id="telepon_penerima">
                                    </div>
                                    <div class="form-group">
                                        <label for="alamat_penerima">Alamat Penerima</label>
                                        <textarea class="form-control" name="alamat_penerima" id="alamat_penerima" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card card-widget">
                        <div class="card-body">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <div align="right">
                                            <h4>Total : <b><span id="faktur"></span></b></h4>
                                            <h1><b><span id="grand_total" style="font-size:35pt"></span></b></h1>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="nofaktur" value="">
            <input type="hidden" name="id_user" value="">

            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-widget">
                        <div class="card-body">
                            <table width="100%">
                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="sub_total">Grand Total</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" id="total" value="" class="form-control"
                                                readonly>
                                            <input type="hidden" name="total" id="total" value=""
                                                class="form-control" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="deposit-used">Deposit Digunakan</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="deposit_used" id="deposit-used"
                                                value="0" class="form-control" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="bayar">Bayar</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" name="bayar" id="bayar" onkeyup="byr()"
                                                class="form-control">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="potongan-harga">Potongan Harga</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="potongan-check"
                                                    name="potongan_check">
                                                <label class="form-check-label" for="potongan-check">Berikan
                                                    Potongan</label>
                                            </div>
                                            <div id="potongan-input-container"
                                                style="display: none; margin-top: 10px;">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="potongan-nominal"
                                                        name="potongan_nominal" placeholder="Nominal Potongan">
                                                </div>
                                                <small class="text-muted">Masukkan nominal potongan harga</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="ongkir">Ongkir</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" class="form-control" id="ongkir"
                                                    name="ongkir" placeholder="Nominal Ongkir">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="packing_kayu">Packing Kayu</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" class="form-control" id="packing_kayu"
                                                    name="packing_kayu" placeholder="Biaya Tambahan Packing Kayu">
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align:top; width:30%">
                                        <label for="diskon-check">Diskon</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="diskon-check"
                                                    name="diskon_check">
                                                <label class="form-check-label" for="diskon-check">Berikan
                                                    Diskon</label>
                                            </div>
                                            <div id="diskon-input-container" style="display: none; margin-top: 10px;">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="diskon-persen"
                                                        name="diskon_persen" min="0" max="100"
                                                        placeholder="Persentase Diskon">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">Masukkan persentase diskon (0-100%)</small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Di dalam form, setelah input bayar -->
                                <tr id="bukti-transfer-container" style="display: none;">
                                    <td style="vertical-align:top; width:30%">
                                        <label for="bukti_transfer">Bukti Transfer</label>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="file" name="bukti_transfer" id="bukti_transfer"
                                                class="form-control-file">
                                            <small class="text-muted">Upload bukti transfer (format: jpg, png, pdf, max
                                                2MB)</small>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" name="discount" value="0">
                            <input type="hidden" name="potongan" value="0">
                            <div align="right">
                                <button type="submit" name="simpan" id="simpan" class="btn btn-primary">
                                    <i class="fa fa-paper-plane"></i> Simpan
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-widget">
                        <div class="card-body">
                            <div align="right">
                                <h4>Kembalian :</h4>
                                <h1><b><span id="kembalian" style="font-size:35pt">0</span></b></h1>
                            </div>
                            <div align="right">
                                <textarea name="notes" id="catatan" class="form-control" rows="3"
                                    placeholder="Masukkan catatan transaksi"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modal-produk" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Data Produk</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="produk-table" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Kode Produk</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Penerbit</th>
                                <th>Supplier</th>
                                <th>Stok</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTables -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="add-to-cart">Tambah ke Cart</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Batal -->
    <div class="modal fade" id="modal-batal" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Hapus Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah yakin ingin hapus...?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">No</button>
                    <a href="" class="btn btn-outline-light">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pelanggan -->
    <div class="modal fade" id="modal-pelanggan" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Data Customer</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ibox-content">
                        <form id="form-customer" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Customer</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                                <small class="form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="no_hp">Nomor HP</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control"
                                        placeholder="81234567890" required>
                                </div>
                                <small class="form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"
                                    required></textarea>
                                <small class="form-text text-danger"></small>
                            </div>
                            <button class="btn btn-primary" type="submit" name="tambah">
                                <i class="fa fa-check"></i>&nbsp;Simpan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <!-- Chosen Select -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#ekspedisi').change(function() {
                if ($(this).val() === 'Lainnya') {
                    $('#ekspedisi-lain-container').show();
                    $('#ekspedisi-logo-container').show();
                } else {
                    $('#ekspedisi-lain-container').hide();
                    $('#ekspedisi-logo-container').hide();
                }
            });

            $('#ekspedisi_logo').change(function(e) {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#ekspedisi-logo-preview').attr('src', e.target.result).show();
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Buat elemen untuk preview
            $('#ekspedisi-logo-container').append('<img id="ekspedisi-logo-preview" src="#" alt="Preview Logo">');
            // Fungsi untuk memuat data ekspedisi
            function loadEkspedisi() {
                $.ajax({
                    url: "{{ route('transaksi.transaksi_penjualan.getEkspedisi') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        var ekspedisiSelect = $('#ekspedisi');
                        ekspedisiSelect.empty();
                        ekspedisiSelect.append('<option value="">Pilih Ekspedisi</option>');

                        $.each(response, function(index, ekspedisi) {
                            ekspedisiSelect.append('<option value="' + ekspedisi
                                .nama_ekspedisi + '">' + ekspedisi.nama_ekspedisi +
                                '</option>');
                        });

                        // Tambahkan opsi Lainnya di akhir
                        ekspedisiSelect.append('<option value="Lainnya">Lainnya</option>');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal memuat data ekspedisi', 'Error');
                        console.error('Error:', xhr.responseText);
                    }
                });
            }

            // Panggil fungsi loadEkspedisi saat dokumen siap
            $(document).ready(function() {
                loadEkspedisi();

                // Handler untuk perubahan select ekspedisi tetap sama
                $('#ekspedisi').change(function() {
                    if ($(this).val() === 'Lainnya') {
                        $('#ekspedisi-lain-container').show();
                    } else {
                        $('#ekspedisi-lain-container').hide();
                    }
                });
            });

            // Handle dropship mode toggle
            $('#dropship-mode').change(function() {
                if ($(this).is(':checked')) {
                    // Disable customer selection
                    $('#customer').prop('disabled', true).val('').trigger('change');
                    $('.input-group-append button').prop('disabled', true);

                    // Show dropship info fields
                    $('.dropship-info').show();

                    // Make ekspedisi required
                    $('#ekspedisi').prop('required', true);

                    // Show custom price column
                    $('body').addClass('dropship-mode');

                    toastr.info('Mode Dropship aktif - Harap isi informasi pengirim dan penerima', 'Info');
                } else {
                    // Enable customer selection
                    $('#customer').prop('disabled', false);
                    $('.input-group-append button').prop('disabled', false);

                    // Hide dropship info fields
                    $('.dropship-info').hide();

                    // Make ekspedisi not required
                    $('#ekspedisi').prop('required', false);

                    // Hide custom price column
                    $('body').removeClass('dropship-mode');

                    toastr.info('Mode Dropship dinonaktifkan', 'Info');
                }
            });

            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#diskon-check').change(function() {
                if ($(this).is(':checked')) {
                    $('#diskon-input-container').show();
                    $('#diskon-persen').val('0').focus();
                } else {
                    $('#diskon-input-container').hide();
                    $('#diskon-persen').val('0');
                }
                updateGrandTotal();
            });

            $('#use-deposit-btn').on('click', function() {
                var availableDeposit = parseRupiah($('#deposit').val()) || 0;
                var grandTotal = parseRupiah($('#grand_total').text()) || 0;
                var currentDepositUsed = parseRupiah($('#deposit-used').val()) || 0;

                if (availableDeposit <= 0) {
                    toastr.warning('Pelanggan tidak memiliki deposit', 'Peringatan');
                    return;
                }

                // Calculate remaining available deposit (total - already used)
                var remainingDeposit = availableDeposit - currentDepositUsed;

                if (remainingDeposit <= 0) {
                    toastr.warning('Deposit sudah habis digunakan', 'Peringatan');
                    return;
                }

                // Calculate how much more we can apply
                var totalNeeded = parseRupiah($('#total').val()) || 0;
                var paymentNeeded = totalNeeded - currentDepositUsed;

                if (paymentNeeded <= 0) {
                    toastr.info('Total sudah terpenuhi oleh deposit', 'Informasi');
                    return;
                }

                // Apply either remaining deposit or needed amount, whichever is smaller
                var depositToUse = Math.min(remainingDeposit, paymentNeeded);

                // Update deposit used
                $('#deposit-used').val(formatRupiah(currentDepositUsed + depositToUse));

                // Auto-fill payment field with remaining amount
                var remainingPayment = totalNeeded - (currentDepositUsed + depositToUse);
                $('#bayar').val(formatRupiah(remainingPayment > 0 ? remainingPayment : 0));

                updateGrandTotal();
                calculateKembalian();

                toastr.success('Deposit digunakan: ' + formatRupiah(depositToUse), 'Berhasil');
            });

            $('#reset-deposit-btn').on('click', function() {
                $('#deposit-used').val('0');
                $('#bayar').val('0');
                updateGrandTotal();
                calculateKembalian();
                toastr.info('Deposit direset', 'Informasi');
            });

            function validateQuantities() {
                var isValid = true;

                $('#cart_table tr').each(function() {
                    var row = $(this);
                    var qty = parseInt(row.find('.qty-input').val()) || 0;
                    var stok = parseInt(row.find('.stok').text()) || 0;

                    if (qty > stok) {
                        isValid = false;
                        // Highlight row yang bermasalah
                        row.addClass('bg-danger text-white');
                        toastr.error(
                            `Quantity untuk produk ${row.find('td:eq(2)').text()} melebihi stok (${stok})`,
                            'Error!');
                    } else {
                        row.removeClass('bg-danger text-white');
                    }
                });

                return isValid;
            }

            // Submit form transaksi
            // Submit form transaksi - bagian yang diperbaiki
            $('#form-transaksi').on('submit', function(e) {
                e.preventDefault();

                if (!validateQuantities()) {
                    toastr.error('Terdapat produk dengan quantity melebihi stok', 'Error!');
                    return false;
                }

                // Validasi dropship mode
                if ($('#dropship-mode').is(':checked')) {
                    if (!$('#nama_pengirim').val() || !$('#telepon_pengirim').val() || !$(
                            '#alamat_pengirim').val() ||
                        !$('#nama_penerima').val() || !$('#telepon_penerima').val() || !$(
                            '#alamat_penerima').val()) {
                        toastr.error('Harap lengkapi semua informasi pengirim dan penerima untuk dropship',
                            'Error!');
                        return false;
                    }

                    if (!$('#ekspedisi').val()) {
                        toastr.error('Ekspedisi harus dipilih untuk mode dropship', 'Error!');
                        return false;
                    }
                }

                // Validasi minimal ada 1 item di cart
                if ($('#cart_table tr').length === 0) {
                    toastr.error('Minimal harus ada 1 produk dalam transaksi', 'Error!');
                    return;
                }

                // Validasi customer dipilih jika bukan dropship
                if (!$('#dropship-mode').is(':checked') && !$('#customer').val()) {
                    toastr.error('Customer harus dipilih', 'Error!');
                    $('#customer').focus();
                    return;
                }

                if ($('#lunas').is(':checked')) {
                    var total = parseRupiah($('#total').val()) || 0;
                    var bayar = parseRupiah($('#bayar').val()) || 0;
                    var depositUsed = parseRupiah($('#deposit-used').val()) || 0;
                    var totalPayment = bayar + depositUsed;

                    if (totalPayment < total) {
                        toastr.error('Pembayaran + Deposit tidak mencukupi untuk transaksi Lunas',
                            'Error!');
                        return;
                    }
                }

                // Siapkan data items
                var items = [];
                $('#cart_table tr').each(function() {
                    var row = $(this);
                    items.push({
                        kd_produk: row.find('td:eq(1)').text(),
                        quantity: parseInt(row.find('.qty-input').val()),
                        unit_price: parseRupiah($('#dropship-mode').is(':checked') ?
                            row.find('.custom-price-input').val() :
                            row.find('.harga-asli').text()),
                        original_price: parseRupiah(row.find('.harga-asli').text())
                    });
                });

                var potonganNominal = parseRupiah($('#potongan-nominal').val()) || 0;
                var usedDeposit = parseRupiah($('#deposit-used').val()) || 0;

                // Siapkan data transaksi
                var transaksiData = {
                    customer: $('#customer').val(),
                    payment_method: $('input[name="metode_pembayaran"]:checked').val(),
                    payment_status: $('input[name="status_pembayaran"]:checked').val(),
                    channel_order: $('#channel_order').val(),
                    items: items,
                    subtotal: parseRupiah($('#grand_total').text()),
                    total: parseRupiah($('#total').val()),
                    paid_amount: parseRupiah($('#bayar').val()),
                    used_deposit: usedDeposit,
                    diskon_persen: parseFloat($('#diskon-persen').val()) || 0,
                    notes: $('#catatan').val() || '',
                    ekspedisi: $('#ekspedisi').val(),
                    ekspedisi_lain: $('#ekspedisi_lain').val() || null, // Pastikan ini dikirim
                    potongan: potonganNominal,
                    is_dropship: $('#dropship-mode').is(':checked') ? 1 : 0,
                    nama_pengirim: $('#nama_pengirim').val() || "",
                    telepon_pengirim: $('#telepon_pengirim').val() || "",
                    alamat_pengirim: $('#alamat_pengirim').val() || "",
                    nama_penerima: $('#nama_penerima').val() || "",
                    telepon_penerima: $('#telepon_penerima').val() || "",
                    alamat_penerima: $('#alamat_penerima').val() || "",
                    ongkir: parseRupiah($('#ongkir').val()) || 0,
                    packing_kayu: parseRupiah($('#packing_kayu').val()) || 0,
                };

                // Jika metode DP, pastikan payment_method adalah 'dp'
                if ($('#hutang').is(':checked') && $('#dp').is(':checked')) {
                    transaksiData.payment_method = 'dp';
                }

                // Show loading state
                const submitBtn = $('#simpan');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                // Cek apakah ada file yang perlu di-upload
                var hasFiles = $('#ekspedisi_logo')[0].files.length > 0 || $('#bukti_transfer')[0].files
                    .length > 0;

                var ajaxOptions = {
                    url: $('#form-transaksi').attr('action'),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                };

                if (hasFiles) {
                    // Jika ada file, gunakan FormData
                    var formData = new FormData();

                    // Append data transaksi ke FormData
                    Object.keys(transaksiData).forEach(key => {
                        if (key === 'items') {
                            // Untuk array items, append setiap item secara terpisah
                            transaksiData[key].forEach((item, index) => {
                                Object.keys(item).forEach(itemKey => {
                                    formData.append(`items[${index}][${itemKey}]`,
                                        item[itemKey]);
                                });
                            });
                        } else if (transaksiData[key] !== null && transaksiData[key] !==
                            undefined) {
                            formData.append(key, transaksiData[key]);
                        }
                    });

                    // Append file ekspedisi_logo jika ada
                    var ekspedisiLogoFile = $('#ekspedisi_logo')[0].files[0];
                    if (ekspedisiLogoFile) {
                        formData.append('ekspedisi_logo', ekspedisiLogoFile);
                    }

                    // Append file bukti_transfer jika ada
                    var buktiTransferFile = $('#bukti_transfer')[0].files[0];
                    if (buktiTransferFile) {
                        formData.append('bukti_transfer', buktiTransferFile);
                    }

                    ajaxOptions.data = formData;
                    ajaxOptions.processData = false;
                    ajaxOptions.contentType = false;
                } else {
                    // Jika tidak ada file, gunakan data biasa
                    ajaxOptions.data = transaksiData;
                }

                // Kirim data ke server
                $.ajax(ajaxOptions).done(function(response) {}).done(function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Transaksi berhasil disimpan!',
                            'Sukses!');

                        var invoiceUrl =
                            "{{ route('transaksi.transaksi_penjualan.cetak_invoice', '') }}/" +
                            response.data.transaksi_id;
                        var invoiceWindow = window.open(invoiceUrl, '_blank');

                        // Redirect ke halaman transaksi setelah 3 detik
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('transaksi.transaksi_penjualan.index') }}";
                        }, 3000);
                    } else {
                        toastr.error(response.message || 'Gagal menyimpan transaksi!', 'Error!');
                    }
                }).fail(function(xhr) {}).fail(function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = [];

                        $.each(errors, function(key, value) {
                            errorMessages.push(value[0]);
                        });

                        toastr.error(errorMessages.join('<br>'), 'Validasi Error!', {
                            timeOut: 8000,
                            extendedTimeOut: 3000,
                            enableHtml: true,
                            closeButton: true,
                            progressBar: true
                        });
                    } else {
                        toastr.error('Terjadi kesalahan pada server', 'Error!');
                    }
                    console.error('Error:', xhr.responseText);
                }).always(function() {
                    submitBtn.prop('disabled', false).html(originalText);
                });
            });


            $('#deposit').on('change input', function() {
                updateGrandTotal();
            });
            $('#grand_total').on('DOMSubtreeModified', calculateTotalAfterDeposit);

            $('input[name="metode_pembayaran"]').change(function() {
                if (this.value === 'transfer') {
                    $('#bukti-transfer-container').show();
                    $('#bukti_transfer').prop('required', true);
                } else {
                    $('#bukti-transfer-container').hide();
                    $('#bukti_transfer').prop('required', false);
                }
            });

            // Pastikan inisialisasi awal sesuai dengan metode yang terpilih
            if ($('#transfer').is(':checked')) {
                $('#bukti-transfer-container').show();
                $('#bukti_transfer').prop('required', true);
            } else {
                $('#bukti-transfer-container').hide();
                $('#bukti_transfer').prop('required', false);
            }

            // Di dalam $(document).ready(function() { ... }), tambahkan:
            // Di dalam $(document).ready(function() { ... }), ubah handler untuk DP:
            $('#dp').change(function() {
                if ($(this).is(':checked')) {
                    $('#bayar').prop('readonly', false).val('Rp 0').focus();
                } else {
                    $('#bayar').prop('readonly', true).val('Rp 0');
                    $('#kembalian').text(formatRupiah(0));
                }
                calculateKembalian();
            });

            // Perbarui juga handler status pembayaran untuk mengakomodasi DP:
            $('input[name="status_pembayaran"]').change(function() {
                if (this.value === 'hutang') {
                    $('#metode-lunas').hide();
                    $('#metode-hutang').show();
                    if (!$('#dp').is(':checked')) {
                        $('#bayar').val('Rp 0').prop('readonly', true);
                    } else {
                        $('#bayar').val('Rp 0').prop('readonly', false);
                    }
                    $('#kembalian').text(formatRupiah(0));
                    $('#bukti-transfer-container').hide();
                    $('#bukti_transfer').prop('required', false);
                } else {
                    $('#metode-lunas').show();
                    $('#metode-hutang').hide();
                    $('#bayar').val('Rp 0').prop('readonly', false);
                    $('#tunai').prop('checked', true);
                    calculateKembalian();

                    if ($('#transfer').is(':checked')) {
                        $('#bukti-transfer-container').show();
                        $('#bukti_transfer').prop('required', true);
                    }
                }
            });

            // Inisialisasi awal untuk DP
            if ($('#hutang').is(':checked') && $('#dp').is(':checked')) {
                $('#bayar').prop('readonly', false);
            }

            // Perbarui inisialisasi awal
            if ($('#hutang').is(':checked')) {
                $('#metode-lunas').hide();
                $('#metode-hutang').show();
                if (!$('#dp').is(':checked')) {
                    $('#bayar').val('0').prop('readonly', true);
                }
                calculateKembalian();
            } else {
                $('#metode-lunas').show();
                $('#metode-hutang').hide();
                $('#bayar').prop('readonly', false);
                $('#tunai').prop('checked', true);
                calculateKembalian();
            }

            $('#dropship-mode').on('change', function() {
                if ($(this).is(':checked')) {
                    $('body').addClass('dropship-mode');
                    $('.custom-price-input').prop('disabled', false);
                    toastr.info('Mode Dropship aktif - Anda dapat mengatur harga custom', 'Info');
                } else {
                    $('body').removeClass('dropship-mode');
                    $('.custom-price-input').prop('disabled', true);

                    // Reset semua harga custom ke harga asli
                    $('#cart_table tr').each(function() {
                        var row = $(this);
                        var hargaAsli = parseRupiah(row.find('.harga-asli').text());
                        row.find('.custom-price-input').val(hargaAsli);
                        updateSubtotal(row);
                    });

                    updateGrandTotal();
                    toastr.info('Mode Dropship dinonaktifkan - Harga kembali ke harga asli', 'Info');
                }
            });

            $('#customer').on('change', function() {
                var selectedCustomer = $(this).select2('data')[0];
                var depositValue = 0;

                if (selectedCustomer && selectedCustomer.deposit) {
                    depositValue = selectedCustomer.deposit;
                }

                $('#deposit').val(formatRupiah(depositValue));
                updateGrandTotal();
            });

            function refreshCustomerSelect2(newCustomerId, customerName, customerPhone, customerDeposit) {
                // Clear existing options kecuali option kosong
                $('#customer').empty();
                $('#customer').append('<option value="">Pilih Pelanggan</option>');

                // Tambahkan customer baru sebagai option dan select
                if (newCustomerId && customerName) {
                    let customerText = customerName + (customerPhone ? ' | ' + customerPhone : '');
                    let newOption = new Option(customerText, newCustomerId, true, true);
                    $('#customer').append(newOption).trigger('change');

                    // Set deposit value jika ada
                    if (customerDeposit) {
                        $('#deposit').val(formatRupiah(customerDeposit));
                    }
                }

                // Trigger change event untuk update Select2
                $('#customer').trigger('change');

                // Refresh tampilan Select2
                $('#customer').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih Pelanggan',
                    allowClear: true,
                    minimumInputLength: 0,
                    ajax: {
                        url: "{{ route('transaksi.transaksi_penjualan.getcustomer') }}",
                        dataType: 'json',
                        type: "POST",
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    templateResult: formatCustomer,
                    templateSelection: formatCustomerSelection
                });

                // Set value customer yang baru ditambahkan
                if (newCustomerId) {
                    $('#customer').val(newCustomerId).trigger('change');
                }
            }

            // Submit form customer
            $('#form-customer').on('submit', function(e) {
                e.preventDefault();

                // Clear previous error messages
                $('#form-customer .text-danger').text('');

                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                // Format nomor HP (pastikan tanpa +62)
                let noHp = $('#no_hp').val();
                if (noHp.startsWith('0')) {
                    noHp = noHp.substring(1);
                }
                $('#no_hp').val(noHp);

                $.ajax({
                    url: '{{ route('transaksi.transaksi_penjualan.simpancustomer') }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Tampilkan notifikasi sukses dengan Toastr
                            toastr.success(response.message || 'Customer berhasil ditambahkan!',
                                'Sukses!');

                            // Tutup modal
                            $('#modal-pelanggan').modal('hide');

                            // Refresh Select2 dengan data customer baru
                            refreshCustomerSelect2(
                                response.data.id,
                                response.data.nama || $('#nama').val(),
                                response.data.no_hp || $('#no_hp').val(),
                                0
                            );

                            // Reset form
                            $('#form-customer')[0].reset();

                        } else {
                            toastr.error(response.message ||
                                'Terjadi kesalahan saat menyimpan customer!', 'Error!');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors - tampilkan HANYA di toast
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = [];

                            $.each(errors, function(key, value) {
                                errorMessages.push(value[0]);
                            });

                            // Tampilkan semua pesan error dalam satu toast
                            if (errorMessages.length > 0) {
                                let combinedMessage = errorMessages.join('<br>');
                                toastr.error(combinedMessage, 'Validasi Error!', {
                                    timeOut: 8000,
                                    extendedTimeOut: 3000,
                                    enableHtml: true,
                                    closeButton: true,
                                    progressBar: true
                                });
                            }

                            // Fokus ke field pertama yang error
                            let firstErrorField = Object.keys(errors)[0];
                            $('[name="' + firstErrorField + '"]').focus();

                        } else if (xhr.status === 500) {
                            // Server error
                            let errorMessage = 'Terjadi kesalahan pada server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage, 'Server Error!');
                        } else {
                            // Unknown error
                            toastr.error('Terjadi kesalahan yang tidak diketahui.', 'Error!');
                        }
                        console.error('Error:', xhr.responseText);
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset form saat modal ditutup
            $('#modal-pelanggan').on('hidden.bs.modal', function() {
                $('#form-customer')[0].reset();
                $('#form-customer .text-danger').text('');
            });

            // Show toast when modal is opened
            $('#modal-pelanggan').on('shown.bs.modal', function() {
                toastr.info('Lengkapi data customer dengan benar', 'Info');
                $('#nama').focus();
            });

            function formatCustomer(customer) {
                if (customer.loading) return customer.text;

                var $container = $(
                    '<div class="customer-item">' +
                    '<strong>' + customer.text.split(' | ')[0] + '</strong>' +
                    '<br><small>' + (customer.text.split(' | ')[1] || '') + '</small>' +
                    '<br><small>Deposit: ' + formatRupiah(customer.deposit || 0) + '</small>' +
                    '</div>'
                );
                return $container;
            }

            function formatCustomerSelection(customer) {
                return customer.text.split(' | ')[0] || customer.text;
            }

            $('#customer').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih Pelanggan',
                theme: 'bootstrap-5',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('transaksi.transaksi_penjualan.getcustomer') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data,
                            pagination: {
                                more: (params.page * 30) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatCustomer,
                templateSelection: formatCustomerSelection
            });

            function formatCustomer(customer) {
                if (customer.loading) return customer.text;

                var $container = $(
                    '<div class="customer-item">' +
                    '<strong>' + customer.text.split(' | ')[0] + '</strong>' +
                    '<br><small>' + (customer.text.split(' | ')[1] || '') + '</small>' +
                    '</div>'
                );
                return $container;
            }

            function formatCustomerSelection(customer) {
                return customer.text.split(' | ')[0] || customer.text;
            }

            var selectedProducts = {}; // Object untuk menyimpan produk yang dipilih

            // Inisialisasi DataTables untuk produk
            var produkTable = $('#produk-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.transaksi_penjualan.load') }}",
                    type: "POST",
                    data: function(d) {
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Cek apakah produk ini sudah dipilih sebelumnya
                            var isChecked = selectedProducts[data] ? 'checked' : '';
                            return '<input type="checkbox" class="produk-check" value="' + data +
                                '" ' + isChecked + '>';
                        }
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
                        data: 'penulis',
                        name: 'penulis'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'penerbit',
                        name: 'penerbit'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'stok',
                        name: 'stok'
                    },
                    {
                        data: 'harga_jual',
                        name: 'harga_jual',
                        render: function(data, type, row) {
                            return formatRupiah(data);
                        }
                    }
                ],
                drawCallback: function(settings) {
                    // Setel ulang checkbox setelah draw (setelah pencarian/paging)
                    var api = this.api();
                    api.rows().nodes().each(function(node) {
                        var data = api.row(node).data();
                        if (data && selectedProducts[data.id]) {
                            $(node).find('.produk-check').prop('checked', true);
                        }
                    });

                    // Update select-all checkbox
                    var allChecked = $('.produk-check:visible:checked').length === $(
                        '.produk-check:visible').length;
                    $('#select-all').prop('checked', allChecked);
                }
            });

            // Fungsi untuk format Rupiah
            function formatRupiah(angka) {
                if (!angka) return 'Rp 0';
                // Pastikan angka adalah number
                var number = typeof angka === 'string' ? parseFloat(angka.replace(/[^0-9]/g, '')) : angka;
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Fungsi untuk konversi dari format Rupiah ke angka
            function parseRupiah(rupiah) {
                if (!rupiah) return 0;
                // Handle jika sudah dalam bentuk angka
                if (!isNaN(rupiah)) return parseFloat(rupiah);
                return parseFloat(rupiah.replace(/[^0-9]/g, ''));
            }

            // Select all checkbox
            $('#select-all').on('click', function() {
                var isChecked = $(this).prop('checked');

                // Dapatkan semua data yang terlihat saat ini
                produkTable.rows({
                    search: 'applied'
                }).nodes().each(function(node) {
                    var data = produkTable.row(node).data();
                    if (data) {
                        $(node).find('.produk-check').prop('checked', isChecked);

                        // Update selectedProducts
                        if (isChecked) {
                            selectedProducts[data.id] = data;
                        } else {
                            delete selectedProducts[data.id];
                        }
                    }
                });
            });

            // Ketika checkbox produk di-klik
            $('#produk-table tbody').on('change', '.produk-check', function() {
                var rowData = produkTable.row($(this).closest('tr')).data();

                if ($(this).prop('checked')) {
                    selectedProducts[rowData.id] = rowData;
                } else {
                    delete selectedProducts[rowData.id];
                }

                // Update select-all checkbox
                var allChecked = $('.produk-check:visible:checked').length === $('.produk-check:visible')
                    .length;
                $('#select-all').prop('checked', allChecked);
            });

            // Tambahkan produk ke cart
            $('#add-to-cart').on('click', function() {
                if (Object.keys(selectedProducts).length === 0) {
                    alert('Pilih minimal satu produk!');
                    return;
                }

                // Konversi object ke array
                var selectedRows = Object.values(selectedProducts);

                // Tambahkan ke cart
                addToCart(selectedRows);

                // Kosongkan selectedProducts setelah ditambahkan ke cart
                selectedProducts = {};

                // Perbarui tampilan checkbox di modal
                produkTable.draw(false);

                // Tutup modal
                $('#modal-produk').modal('hide');
            });

            function addToCart(products) {
                products.forEach(function(product) {
                    var existingItem = $('#cart_table tr[data-id="' + product.id + '"]');

                    if (existingItem.length > 0) {
                        var qtyInput = existingItem.find('.qty-input');
                        var currentQty = parseInt(qtyInput.val());
                        qtyInput.val(currentQty + 1);
                        updateSubtotal(existingItem);
                    } else {
                        var newRow = '<tr data-id="' + product.id + '">' +
                            '<td>' + ($('#cart_table tr').length + 1) + '</td>' +
                            '<td>' + product.kd_produk + '</td>' +
                            '<td>' + product.judul + '</td>' +
                            '<td>' + product.supplier + '</td>' +
                            '<td class="harga-asli" data-value="' + product.harga_jual + '">' +
                            formatRupiah(product.harga_jual) + '</td>' +
                            '<td class="custom-price-cell"><input type="text" class="form-control custom-price-input" value="' +
                            formatRupiah(product.harga_jual) + '" style="width: 100%;"></td>' +
                            '<td>' +
                            '<input type="number" class="form-control qty-input" value="1" min="1" style="width: 70px;">' +
                            '<span class="stok" style="display:none;">' + product.stok + '</span>' +
                            // Add hidden stock info
                            '</td>' +
                            '<td class="subtotal">' + formatRupiah(product.harga_jual) + '</td>' +
                            '<td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button></td>' +
                            '</tr>';

                        $('#cart_table').append(newRow);
                    }
                });
                updateGrandTotal();

                $('.qty-input').off('change').on('change', function() {
                    updateSubtotal($(this).closest('tr'));
                    updateGrandTotal();
                });

                $('.custom-price-input').off('change').on('change', function() {
                    updateSubtotal($(this).closest('tr'));
                    updateGrandTotal();
                });

                $('.remove-item').off('click').on('click', function() {
                    $(this).closest('tr').remove();
                    updateRowNumbers();
                    updateGrandTotal();
                });
            }
            // Event handler untuk input Rupiah
            $(document).on('keyup', '.custom-price-input', function(e) {
                $(this).val(formatRupiahInput($(this).val(), 'Rp '));
                updateSubtotal($(this).closest('tr'));
                updateGrandTotal();
            });

            // Event handler saat input kehilangan fokus
            $(document).on('blur', '.custom-price-input', function() {
                var value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
                updateSubtotal($(this).closest('tr'));
                updateGrandTotal();
            });
            // Fungsi untuk format input Rupiah
            function formatRupiahInput(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
            }

            function updateSubtotal(row) {
                var harga = 0;
                if ($('#dropship-mode').is(':checked')) {
                    var hargaInput = row.find('.custom-price-input').val();
                    harga = parseRupiah(hargaInput) || 0;
                } else {
                    harga = parseRupiah(row.find('.harga-asli').text());
                }

                var qty = parseInt(row.find('.qty-input').val()) || 0;
                var stok = parseInt(row.find('.stok').text()) || 0;

                if (qty > stok) {
                    row.addClass('bg-danger text-white');
                    toastr.error('Quantity melebihi stok yang tersedia (' + stok + ')', 'Error!');
                    row.find('.qty-input').val(stok);
                    qty = stok;
                } else {
                    row.removeClass('bg-danger text-white');
                }

                var subtotal = harga * qty;
                row.find('.subtotal').text(formatRupiah(subtotal));
            }

            $('#potongan-check').change(function() {
                if ($(this).is(':checked')) {
                    $('#potongan-input-container').show();
                    $('#potongan-nominal').val('0').focus();
                } else {
                    $('#potongan-input-container').hide();
                    $('#potongan-nominal').val('0');
                }
                updateGrandTotal();
            });

            $('#ongkir, #packing_kayu').on('keyup', function() {
                $(this).val(formatRupiahInput($(this).val(), ''));
                updateGrandTotal();
            });

            $('#ongkir, #packing_kayu').on('blur', function() {
                var value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
                updateGrandTotal();
            });

            // Format input potongan nominal
            $('#potongan-nominal').on('keyup', function(e) {
                $(this).val(formatRupiahInput($(this).val(), ''));
            });

            $('#potongan-nominal').on('blur', function() {
                var value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
                updateGrandTotal();
            });

            function updateGrandTotal() {
                var grandTotal = 0;

                // Calculate subtotal from all items
                $('#cart_table tr').each(function() {
                    var subtotalText = $(this).find('.subtotal').text().replace('Rp ', '').replace(/\./g,
                        '');
                    grandTotal += parseFloat(subtotalText) || 0;
                });

                // Calculate potongan harga if any
                var potonganNominal = 0;
                if ($('#potongan-check').is(':checked')) {
                    potonganNominal = parseRupiah($('#potongan-nominal').val()) || 0;
                    if (potonganNominal < 0) potonganNominal = 0;
                    if (potonganNominal > grandTotal) potonganNominal = grandTotal;
                }

                // Calculate discount if any
                var diskonPersen = 0;
                var diskonNominal = 0;

                if ($('#diskon-check').is(':checked')) {
                    diskonPersen = parseFloat($('#diskon-persen').val()) || 0;
                    if (diskonPersen < 0) diskonPersen = 0;
                    if (diskonPersen > 100) diskonPersen = 100;
                    diskonNominal = (grandTotal - potonganNominal) * (diskonPersen / 100);
                }

                var ongkir = parseRupiah($('#ongkir').val()) || 0;
                var packingKayu = parseRupiah($('#packing_kayu').val()) || 0;
                var totalSetelahPotonganDanDiskon = grandTotal - potonganNominal - diskonNominal + ongkir +
                    packingKayu;

                // Update grand_total display
                $('#grand_total').text(formatRupiah(grandTotal));

                // Update total field (before deposit/payment)
                $('#total').val(formatRupiah(totalSetelahPotonganDanDiskon));

                // Update values for server submission
                $('input[name="subtotal"]').val(grandTotal);
                $('input[name="potongan"]').val(potonganNominal);
                $('input[name="discount"]').val(diskonNominal);
                $('input[name="total"]').val(totalSetelahPotonganDanDiskon);

                calculateKembalian();
            }

            $('#potongan-nominal').on('input change', function() {
                var value = parseRupiah($(this).val()) || 0;
                if (value < 0) {
                    $(this).val(0);
                }
                updateGrandTotal();
            });

            // Event handler untuk input persentase diskon
            $('#diskon-persen').on('input change', function() {
                var value = parseFloat($(this).val()) || 0;
                if (value < 0) {
                    $(this).val(0);
                } else if (value > 100) {
                    $(this).val(100);
                }
                updateGrandTotal();
            });

            $('#form-transaksi').append(
                '<input type="hidden" name="subtotal" value="0">' +
                '<input type="hidden" name="discount" value="0">' +
                '<input type="hidden" name="total" value="0">'
            );
            // Fungsi untuk update nomor urut row
            function updateRowNumbers() {
                $('#cart_table tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Fungsi byr() dan inisialisasi lainnya tetap sama
            function byr() {
                var total = parseRupiah($('#total').val()) || 0;
                var bayar = parseRupiah($('#bayar').val()) || 0;
                var depositUsed = parseRupiah($('#deposit-used').val()) || 0;
                var totalPayment = bayar + depositUsed;
                var kembalian = totalPayment - total;

                // Jika status hutang dan DP tidak dicentang, kembalian selalu 0
                if ($('#hutang').is(':checked') && !$('#dp').is(':checked')) {
                    kembalian = 0;
                }

                $('#kembalian').text(formatRupiah(kembalian));

                // Tampilkan warna merah jika kurang, hijau jika lebih
                if (kembalian < 0) {
                    $('#kembalian').addClass('text-danger').removeClass('text-success');
                } else {
                    $('#kembalian').addClass('text-success').removeClass('text-danger');
                }
            }

            $('#bayar').on('keyup', function(e) {
                // Format nilai saat mengetik
                $(this).val(formatRupiahInput($(this).val(), 'Rp '));

                // Hitung kembalian
                calculateKembalian();
            });

            $('#bayar').on('blur', function() {
                var value = parseRupiah($(this).val());
                $(this).val(formatRupiah(value));
                calculateKembalian();
            });
            // Di dalam $(document).ready(function() { ... }), tambahkan:
            // Real-time calculation for kembalian
            $('#bayar').on('input', calculateKembalian);
            $('#total').on('change', calculateKembalian);
            $('#dp').change(calculateKembalian);
            $('input[name="status_pembayaran"]').change(calculateKembalian);

            // Fungsi untuk menghitung kembalian secara realtime
            function calculateKembalian() {
                var total = parseRupiah($('#total').val()) || 0;
                var bayar = parseRupiah($('#bayar').val()) || 0;
                var depositUsed = parseRupiah($('#deposit-used').val()) || 0;
                var totalPayment = bayar + depositUsed;
                var kembalian = 0;

                if ($('#hutang').is(':checked')) {
                    if ($('#dp').is(':checked')) {
                        // For DP, payment cannot exceed total
                        if (totalPayment > total) {
                            toastr.warning('Pembayaran tidak boleh melebihi total', 'Peringatan');
                            $('#bayar').val(formatRupiah(total - depositUsed));
                            bayar = total - depositUsed;
                            totalPayment = total;
                        }
                        kembalian = 0;
                    } else {
                        // For full hutang, no payment needed
                        $('#bayar').val('0').prop('readonly', true);
                        $('#deposit-used').val('0');
                        kembalian = 0;
                    }
                } else {
                    kembalian = totalPayment - total;

                    // Don't allow negative change (means underpayment)
                    if (kembalian < 0) {
                        kembalian = 0;
                    }
                }

                // Update display
                $('#kembalian').text(formatRupiah(kembalian));

                // Set color based on payment status
                if (totalPayment < total) {
                    $('#kembalian').addClass('text-danger').removeClass('text-success');
                } else {
                    $('#kembalian').addClass('text-success').removeClass('text-danger');
                }
            }
            // Panggil fungsi saat pertama kali load
            calculateKembalian();

            $(".chosen-select").chosen();

            // Disable bayar field if status is hutang
            $('input[name="status_pembayaran"]').change(function() {
                if (this.value === 'hutang') {
                    $('#bayar').val('0').prop('readonly', true);
                    $('#kembalian').text('0');
                } else {
                    $('#bayar').prop('readonly', false);
                }
            });


            function calculateTotalAfterDeposit() {
                // Panggil updateGrandTotal untuk recalculate semua
                updateGrandTotal();
            }
        });
    </script>
</body>

</html>
