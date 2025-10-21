<!DOCTYPE html>
<html>

<head>
    <title>Invoice #{{ $transaksi->kode_transaksi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 0;
            border: 2px solid #000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Section */
        .header {
            padding: 0;
            border-bottom: none;
            background: white;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 2px solid #000;
        }

        /* Tambahkan ini ke bagian CSS */
        .faktur-top {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }

        .colon-align {
            display: inline-block;
            width: 10px;
            text-align: center;
            margin: 0 5px;
        }

        .shipping-info-row span:nth-child(2) {
            display: inline-block;
            width: 10px;
            text-align: center;
            margin: 0 5px;
        }

        .ekspedisi-top {
            justify-content: space-between;
            align-items: center;
            width: 108%;
            font-size: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }



        .logo {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
        }


        .company-details {
            flex: 1;
        }

        .arabic-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin: 0;
            direction: rtl;
            text-align: right;
            font-family: 'Times New Roman', serif;
            line-height: 1.2;
        }

        .english-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin: 5px 0 0 0;
            letter-spacing: 1px;
        }

        .contact-section {
            text-align: center;
            border: 2px solid #000;
            border-radius: 15px;
            padding: 8px 15px;
            background: #f8f9fa;
        }

        .phone-number {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 5px;
            text-decoration: none;
        }

        .address-info {
            font-size: 10px;
            color: #333;
            margin-top: 3px;
            line-height: 1.2;
        }

        /* Social Media Bar */
        .social-media-bar {
            background: #f8f9fa;
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .social-item {
            display: flex;
            align-items: center;
            gap: 3px;
            color: #333;
        }

        .social-icon {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            display: inline-block;
            text-align: center;
            line-height: 16px;
            font-size: 10px;
            color: white;
            font-weight: bold;
        }

        .social-icon.telegram {
            background: #0088cc;
        }

        .social-icon.instagram {
            background: #E4405F;
        }

        .social-icon.twitter {
            background: #1DA1F2;
        }

        .social-icon.facebook {
            background: #1877F2;
        }

        .social-icon.website {
            background: #6c757d;
        }

        .social-icon.tiktok {
            background: #000;
        }

        .social-icon.youtube {
            background: #FF0000;
        }

        /* Invoice Details */
        .invoice-details {
            padding: 10px 20px;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }

        .invoice-details p {
            margin: 3px 0;
            line-height: 1.3;
        }

        /* Products Section */
        .products-section {
            padding: 0;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table th {
            background: #f8f8f8;
            padding: 8px 6px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #000;
        }

        .products-table td {
            padding: 8px 6px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #000;
            vertical-align: middle;
            height: 25px;
        }

        .products-table .arabic-text {
            direction: rtl;
            text-align: right;
            font-family: 'Times New Roman', serif;
            font-size: 11px;
        }

        .products-table .publisher {
            text-align: center;
        }

        /* Notes Section */
        .notes-section {
            padding: 10px 20px;
            border-top: 1px solid #000;
            font-size: 11px;
        }

        .notes-section h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            font-weight: bold;
        }

        .notes-section p {
            margin: 3px 0;
            line-height: 1.3;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-around;
            padding: 15px 20px;
            border-top: 1px solid #000;
            background: white;
        }

        .signature-box {
            text-align: center;
            width: 120px;
        }

        .signature-box p {
            margin: 5px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .signature-line {
            border-bottom: 1px dotted #000;
            height: 30px;
            margin: 10px 0 5px 0;
        }

        /* Bottom Section */
        .bottom-section {
            border-top: 2px dashed #000;
        }

        .shipping-details {
            display: flex;
            background: white;
            min-height: 200px;
            border-bottom: 1px solid #000;
        }

        .shipping-left {
            width: 50%;
            padding: 15px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .shipping-right {
            width: 50%;
            padding: 15px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            border-left: 2px solid #000;
            box-sizing: border-box;
        }

        .shipping-info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 11px;
        }

        .shipping-info-row.full-width {
            flex-direction: column;
        }

        .shipping-info-row.pengirim-section {
            display: block;
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
            padding-bottom: 8px;
        }

        .pengirim-content {
            display: flex;
        }

        .pengirim-left {
            width: 100%;
        }

        .pengirim-item {
            margin-bottom: 8px;
        }

        .pengirim-item span {
            display: inline-block;
            width: 110px;
        }

        .pengirim-line {
            display: inline-block;
            width: calc(100% - 120px);
            border-bottom: 1px solid #000;
            margin-left: 5px;
            padding-bottom: 2px;
        }

        .shipping-info-label {
            width: 120px;
            font-weight: normal;
        }

        .shipping-info-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .full-width-line {
            width: 100%;
            border-bottom: 1px solid #000;
            margin-top: 3px;
            padding-bottom: 2px;
        }

        .ekspedisi-header {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 1px solid #000;
            width: 100%;
            padding: 10px 20px;
        }

        .company-logo-section {
            margin-top: auto;
            text-align: center;
            padding-top: 20px;
        }

        .company-logo-bottom {
            max-width: 80px;
            height: auto;
            opacity: 0.4;
            margin-bottom: 5px;
        }

        .company-name-bottom {
            font-size: 9px;
            color: #666;
            line-height: 1.2;
        }

        .shipping-logo {
            max-width: 200px;
            height: auto;
        }

        /* Final Warning */
        .final-warning {
            color: #000;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-bottom: 1px solid #000;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .no-print {
                display: none;
            }

            .invoice-box {
                border: 2px solid #000;
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }

            .final-warning {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: #000 !important;
                color: white !important;
            }

            .social-media-bar {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        /* Print Buttons */
        .print-buttons {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
        }

        .print-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
        }

        .close-button {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .full-width-divider {
            position: relative;
            margin: 10px -15px;
            /* Sesuaikan dengan padding parent */
            height: 2px;
            background: #000;
        }

        /* Untuk memastikan garis menyatu dengan border */
        .shipping-details {
            display: flex;
            background: white;
            min-height: 200px;
            border-bottom: 1px solid #000;
        }

        .company-section-wrapper {
            width: 100%;
        }

        .company-section {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header Section -->
        @if (!$transaksi->is_dropship || $transaksi->is_dropship == 0)
            <div class="header">
                <div class="header-top">
                    <div class="company-section-wrapper">
                        <div class="company-section">
                            <img src="{{ asset('image/kop_putih.png') }}" alt="Kop Invoice" class="logo">
                        </div>
                    </div>



                </div>



            </div>
        @endif

        <div class="invoice-details">
            <div class="shipping-info-row">
                <span class="shipping-info-label">Nomor Faktur</span>
                <span>:</span>
                <span class="shipping-info-value">{{ $transaksi->nomor_urut }}</span>
            </div>
            <div class="shipping-info-row">
                <span class="shipping-info-label">Tanggal</span>
                <span>:</span>
                <span class="shipping-info-value">{{ date('d-m-Y', strtotime($transaksi->created_at)) }}</span>
            </div>
            <div class="shipping-info-row">
                <span class="shipping-info-label">Kepada Yth</span>
                <span>:</span>
                <span class="shipping-info-value">{{ $transaksi->nama_penerima ?? $transaksi->nama_customer }}</span>
            </div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 35%;">judul</th>
                        <th style="width: 20%;">nama penerbit</th>
                        <th style="width: 10%;">quantity</th>
                        <th style="width: 10%;">✓/✗</th>
                        <th style="width: 17%;">keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="arabic-text">{{ $item->judul }}</td>
                            <td class="publisher">{{ $item->penulis }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach


                </tbody>
            </table>
        </div>

        <!-- Notes Section -->
        <div class="notes-section">
            <h4>PERHATIAN!!</h4>
            <p>- Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali cacat produksi</p>
            <p>- Komplain barang atau nota maksimal 7 hari setelah barang diterima</p>

            <div class="signature-section">
                <div class="signature-box">
                    <p>Admin Penjualan</p>
                    <div class="signature-line"></div>

                </div>
                <div class="signature-box">
                    <p>Quality Control</p>
                    <div class="signature-line"></div>

                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <div class="faktur-top">
                <span class="shipping-info-label">Nomor Faktur</span>
                <span class="colon-align">:</span>
                <span class="shipping-info-value">{{ $transaksi->nomor_urut }}</span>
            </div>

            <div class="shipping-details">
                <div class="shipping-left">
                    <!-- Pengirim Section -->
                    <div class="shipping-info-group pengirim-section">
                        <div class="shipping-info-row">
                            <span class="shipping-info-label">Pengirim</span>
                            <span class="colon-align">:</span>
                            <span class="shipping-info-value">
                                {{ $transaksi->nama_pengirim ?? ($profilPerusahaan->nama_latin ?? 'DAR IBNU ABBAS') }}
                            </span>
                        </div>
                        <div class="shipping-info-row">
                            <span class="shipping-info-label">Nomor Pengirim</span>
                            <span class="colon-align">:</span>
                            <span class="shipping-info-value">
                                {{ $transaksi->telepon_pengirim ?? ($profilPerusahaan->no_telepon ?? '+6289580610975') }}
                            </span>
                        </div>
                        @if ($transaksi->alamat_pengirim)
                            <div class="shipping-info-row">
                                <span class="shipping-info-label">Alamat Pengirim</span>
                                <span class="colon-align">:</span>
                                <span class="shipping-info-value">{{ $transaksi->alamat_pengirim }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Garis Pemisah Full Width -->
                    <div class="full-width-divider"></div>

                    <!-- Penerima Section -->
                    <div class="shipping-info-group penerima-section">
                        <div class="shipping-info-row">
                            <span class="shipping-info-label">Penerima</span>
                            <span class="colon-align">:</span>
                            <span class="shipping-info-value">
                                {{ $transaksi->nama_penerima ?? $transaksi->nama_customer }}
                            </span>
                        </div>
                        <div class="shipping-info-row">
                            <span class="shipping-info-label">Nomor Penerima</span>
                            <span class="colon-align">:</span>
                            <span class="shipping-info-value">
                                @if ($transaksi->telepon_penerima)
                                    {{ $transaksi->telepon_penerima }}
                                @else
                                    +62{{ $transaksi->no_hp_customer }}
                                @endif
                            </span>
                        </div>
                        <div class="shipping-info-row">
                            <span class="shipping-info-label">Alamat Penerima</span>
                            <span class="colon-align">:</span>
                            <span class="shipping-info-value">
                                {{ $transaksi->alamat_penerima ?? ($transaksi->alamat_customer ?? '-') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="shipping-right">
                    <div class="ekspedisi-top">
                        <span class="shipping-info-label">Ekspedisi</span>
                    </div>
                    @if ($transaksi->nama_ekspedisi)
                        <img src="{{ asset('storage/' . $transaksi->ekspedisi_logo) }}"
                            alt="{{ $transaksi->nama_ekspedisi }}" class="shipping-logo">
                    @else
                        <img src="https://via.placeholder.com/200x80/FF0000/FFFFFF?text=LOGO+EKSPEDISI" alt="Ekspedisi"
                            class="shipping-logo">
                    @endif

                </div>
            </div>
        </div>

        <!-- Final Warning -->
        <div class="final-warning">
            🕌 AL QU'AN JANGAN DI BANTING INJAK BASAH 🕌
        </div>

        <!-- Print Buttons -->
        <div class="no-print print-buttons">
            <a href="{{ route('transaksi.transaksi_penjualan.invoice.thermal', $transaksi->id) }}" target="_blank"
                class="print-button">
                Cetak Thermal
            </a>
            <button onclick="window.print()" class="print-button">Cetak Invoice</button>
            <button onclick="window.close()" class="close-button">Tutup</button>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                // Uncomment the line below if you want auto-print
                // window.print();
            }, 500);
        };
    </script>
</body>

</html>
