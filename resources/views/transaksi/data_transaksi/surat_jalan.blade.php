<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $profilPerusahaan->nama_perusahaan ?? 'Dar Ibnu Abbas' }}</title>
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
            border: 2px solid #000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            /* padding akan dipindahkan ke konten selain header */
        }

        /* Header kop tetap menempel penuh */
        .header-container {
            width: 100%;
        }

        .logo-full {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Konten lain diberi padding agar ada jarak dengan border */
        .document-title,
        .customer-info,
        .items-table,
        .payment-section,
        .signature-section {
            padding: 15px;
            /* sesuaikan jarak */
        }

        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 20px 0;
            text-decoration: underline;
        }

        /* contoh jika mau lebih rapi lagi, bisa wrap konten lain dengan container */
        .content-container {
            padding: 15px;
        }


        .company-info {
            color: white;
        }

        .company-name-ar {
            font-size: 20px;
            font-weight: bold;
            direction: rtl;
            text-align: right;
        }

        .company-name-en {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .contact-info {
            text-align: right;
            font-size: 11px;
            color: white;
        }

        .contact-box {
            background: white;
            color: #1e3a8a;
            padding: 6px 10px;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .social-icons {
            margin-top: 5px;
            font-size: 9px;
        }

        .social-icons span {
            margin-right: 10px;
        }

        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 20px 0;
            text-decoration: underline;
        }

        .customer-info {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .customer-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px dotted #666;
            min-height: 20px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
        }

        .items-table th {
            background: #1e3a8a;
            color: white;
            font-weight: bold;
        }

        .items-table td {
            vertical-align: middle;
        }

        .items-table .text-left {
            text-align: left;
        }

        .items-table .text-right {
            text-align: right;
        }

        .payment-section {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .payment-info-section {
            flex: 1;
        }

        .payment-info {
            background: #1e3a8a;
            color: white;
            padding: 8px 12px;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 12px;
        }

        .payment-details {
            background: #e2e8f0;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .payment-details div {
            margin-bottom: 4px;
        }

        .notes {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .notes-content {
            background: #f1f5f9;
            padding: 12px;
            font-size: 11px;
            border-radius: 4px;
        }

        .summary-section {
            width: 320px;
            flex-shrink: 0;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ccc;
            font-size: 11px;
        }

        .summary-table .total-row {
            background: #1e3a8a;
            color: white;
            font-weight: bold;
        }

        .summary-table .text-right {
            text-align: right;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 15mm;
            }

            body,
            .items-table td,
            .items-table th,
            .customer-info,
            .payment-details,
            .notes-content,
            .summary-table td {
                font-size: 12px !important;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header-container">
            <img src="{{ asset('image/kop_warna.png') }}" alt="Kop Invoice" class="logo-full">
        </div>




        <div class="customer-info">
            <div class="customer-row">
                <span class="info-label">Nomor:</span>
                <span class="info-value">{{ $transaksi->nomor_urut }}</span>
            </div>
            <div class="customer-row">
                <span class="info-label">Tanggal:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d F Y') }}</span>
            </div>
            <div class="customer-row">
                <span class="info-label">Kepada Yth:</span>
                <span class="info-value">{{ $transaksi->nama_customer }}</span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">رقم<br>No</th>
                    <th width="35%">اسم الكتاب<br>Nama Buku</th>
                    <th width="15%">المؤلف<br>Penulis</th>
                    <th width="15%">الطبعة<br>Cetakan</th>
                    <th width="8%">الكمية<br>Qty</th>
                    <th width="12%">سعر<br>Harga</th>
                    @if ($items->sum('diskon_produk') > 0)
                        <th width="12%">سعر<br>Diskon</th>
                    @endif
                    <th width="10%">الإجمالي<br>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $item->judul }}</td>
                        <td class="text-left">{{ $item->penulis ?? '-' }}</td>
                        <td class="text-left">{{ $item->penerbit ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        @if ($items->sum('diskon_produk') > 0)
                            <td class="text-right">
                                @if ($item->diskon_produk > 0)
                                    {{ intval($item->diskon_produk) }}%
                                @else
                                    -
                                @endif
                            </td>
                        @endif
                        <td class="text-right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="payment-section">
            <div class="payment-info-section">
                <div class="payment-info">Pembayaran dapat dilakukan pada No Rekening:</div>

                <div class="payment-details">

                    @php
                        $bankList = json_decode($profilPerusahaan->banks ?? '[]');
                    @endphp

                    @foreach ($bankList as $bank)
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">

                            {{-- LOGO BANK --}}
                            <img src="{{ asset('storage/' . $bank->logo_bank) }}" alt="Logo {{ $bank->nama_bank }}"
                                style="height: 22px; margin-right: 8px;">

                            {{-- INFO REKENING --}}
                            <div>
                                <strong>{{ strtoupper($bank->nama_bank) }}</strong> :
                                {{ $bank->no_rek }}<br>
                                <small>A.N. {{ strtoupper($bank->nama_pemilik) }}</small>
                            </div>

                        </div>
                    @endforeach

                </div>



                <div class="notes">PERHATIAN!!</div>
                <div class="notes-content">
                    - Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali cacat produksi<br>
                    - Komplain barang atau nota maksimal 7 hari setelah barang diterima<br>
                    - Harap konfirmasi setelah barang diterima
                </div>
            </div>

            @php
                $subtotal = $transaksi->subtotal ?? $items->sum('total_price');

                // POTONGAN (langsung angka dari database)
                $potongan = $transaksi->potongan ?? 0;

                // DISKON PERSEN (contoh 10 berarti 10%)
                $discountPercent = $transaksi->discount ?? 0;

                // Hitung diskon rupiah
                $discountValue = 0;
                if ($discountPercent > 0) {
                    $discountValue = ($subtotal - $potongan) * ($discountPercent / 100);
                }
            @endphp

            <div class="summary-section">
                <table class="summary-table">

                    {{-- SUBTOTAL --}}
                    <tr>
                        <td><strong>Subtotal</strong></td>
                        <td class="text-right"><strong>
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </strong></td>
                    </tr>

                    @if ($potongan > 0)
                        <tr>
                            <td>Potongan</td>
                            <td class="text-right text-danger">
                                - Rp {{ number_format($potongan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- DISKON (DALAM PERSEN → RUPIAH) --}}
                    @if ($discountPercent > 0)
                        <tr>
                            <td>Diskon ({{ intval($discountPercent) }}%)</td>
                            <td class="text-right text-danger">
                                - {{ intval($discountPercent) }}%
                            </td>
                        </tr>
                    @endif



                    {{-- ONGKIR --}}
                    @if (($transaksi->ongkir ?? 0) > 0)
                        <tr>
                            <td>Ongkos Kirim</td>
                            <td class="text-right">
                                Rp {{ number_format($transaksi->ongkir, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                    @if (($transaksi->packing_kayu ?? 0) > 0)
                        <tr>
                            <td>Packing Tambahan</td>
                            <td class="text-right">
                                Rp {{ number_format($transaksi->packing_kayu, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- POTONGAN --}}

                    {{-- GRAND TOTAL --}}
                    <tr class="total-row">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</strong>
                        </td>
                    </tr>

                </table>
            </div>

        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div>Pengirim</div>
                <div class="signature-line">{{ $profilPerusahaan->nama_perusahaan ?? 'DAR IBNU ABBAS' }}</div>
            </div>
            <div class="signature-box">
                <div>Penerima</div>
                <div class="signature-line">{{ $transaksi->nama_customer }}</div>
            </div>
        </div>
    </div>
</body>

</html>
