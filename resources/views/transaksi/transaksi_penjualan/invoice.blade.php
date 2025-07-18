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
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .company-info {
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .invoice-details {
            margin: 20px 0;
        }

        .customer-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .customer-info,
        .shipping-info {
            width: 48%;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }

        .shipping-info {
            background-color: #f9f9f9;
        }

        .warning-container {
            margin: 15px 0;
            position: relative;
        }

        .warning-note {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ffeeba;
        }

        .dotted-line {
            height: 1px;
            background: repeating-linear-gradient(to right, #333, #333 5px, transparent 5px, transparent 10px);
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            background: #f5f5f5;
            padding: 8px;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .social-media {
            margin-top: 15px;
            font-size: 12px;
        }

        .social-media p {
            margin: 3px 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .invoice-box {
                box-shadow: none;
                border: none;
            }

            .dotted-line {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: repeating-linear-gradient(to right, #000, #000 5px, transparent 5px, transparent 10px);
            }

            .warning-note {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                @if ($profilPerusahaan && $profilPerusahaan->logo)
                    <img src="{{ asset('storage/' . $profilPerusahaan->logo) }}"
                        alt="{{ $profilPerusahaan->nama_toko }} Logo" class="logo">
                @endif
                <h1>{{ $profilPerusahaan->nama_toko ?? 'Dar Ibn Abbas' }}</h1>
                <p>{{ $profilPerusahaan->alamat ?? 'Jl. Jakarta Barat No. 123' }}<br>
                    Jakarta, Indonesia<br>
                    Telp: +62{{ $profilPerusahaan->no_wa ?? '+62895806109754' }}</p>
                <div class="social-media">
                    <p>Website: daribnuabbas.com</p>
                    @if ($profilPerusahaan && $profilPerusahaan->ig)
                        <p>Instagram: {{ $profilPerusahaan->ig }}</p>
                    @endif
                    @if ($profilPerusahaan && $profilPerusahaan->telegram)
                        <p>Telegram: {{ $profilPerusahaan->telegram }}</p>
                    @endif
                    @if ($profilPerusahaan && $profilPerusahaan->no_wa)
                        <p>WhatsApp: +62{{ $profilPerusahaan->no_wa }}</p>
                    @endif
                </div>
            </div>
            <div class="company-info">
                <div class="invoice-title">INVOICE</div>
                <p><strong>No. Transaksi:</strong> {{ $transaksi->kode_transaksi }}</p>
                <p><strong>Tanggal:</strong> {{ date('d/m/Y H:i', strtotime($transaksi->created_at)) }}</p>
                <p><strong>Kasir:</strong> {{ auth()->user()->name }}</p>
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-info">
                <h3>Informasi Customer</h3>
                <p><strong>Nama:</strong> {{ $transaksi->nama_customer }}</p>
                <p>
                    <stronwg>No. HP:</stronwg> +62{{ $transaksi->no_hp_customer }}
                </p>
                <p><strong>Alamat:</strong> {{ $transaksi->alamat_customer }}</p>
                <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->payment_method) }}</p>
                <p><strong>Status:</strong> {{ ucfirst($transaksi->payment_status) }}</p>
            </div>

            <div class="shipping-info">
                <h3>Informasi Pengiriman</h3>
                <p><strong>Ekspedisi:</strong>
                    {{ $transaksi->ekspedisi }}
                    @if ($transaksi->ekspedisi == 'Lainnya')
                        ({{ $transaksi->ekspedisi_lain }})
                    @endif
                </p>
                <p><strong>Nama Penerima:</strong> {{ $transaksi->nama_customer }}</p>
                <p><strong>No. HP Penerima:</strong> +62{{ $transaksi->no_hp_customer }}</p>
                <p><strong>Alamat Pengiriman:</strong> {{ $transaksi->alamat_customer }}</p>
                <p><strong>Catatan:</strong> Packing rapat dan aman</p>
            </div>
        </div>

        <div class="warning-container">
            <div class="warning-note">
                ⚠️ PERHATIAN: JANGAN DIBANTING, INI KITAB! ⚠️
            </div>
            <div class="dotted-line"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Produk</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->kd_produk }}</td>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->penulis }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table>
                <tr>
                    <td width="80%"><strong>Subtotal</strong></td>
                    <td class="text-right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if ($transaksi->potongan > 0)
                    <tr>
                        <td><strong>Potongan Harga</strong></td>
                        <td class="text-right">- Rp {{ number_format($transaksi->potongan, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($transaksi->discount > 0)
                    <tr>
                        <td><strong>Diskon ({{ $transaksi->discount }}%)</strong></td>
                        <td class="text-right">- Rp
                            {{ number_format($transaksi->subtotal * ($transaksi->discount / 100), 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Dibayar</strong></td>
                    <td class="text-right">Rp {{ number_format($transaksi->paid_amount, 0, ',', '.') }}</td>
                </tr>
                @if ($transaksi->change_amount > 0)
                    <tr>
                        <td><strong>Kembalian</strong></td>
                        <td class="text-right">Rp {{ number_format($transaksi->change_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($transaksi->remaining_amount > 0)
                    <tr>
                        <td><strong>Sisa Hutang</strong></td>
                        <td class="text-right">Rp {{ number_format($transaksi->remaining_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>



        <div class="footer">
            <p>Terima kasih telah berbelanja di {{ $profilPerusahaan->nama_toko ?? 'Dar Ibn Abbas' }}</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada kerusakan</p>
            <div class="social-media">
                @if ($profilPerusahaan && ('daribnuabbas.com' || $profilPerusahaan->ig))
                    <p>

                        Website: daribnuabbas.com
                        @if ($profilPerusahaan->ig)
                            | Instagram: {{ $profilPerusahaan->ig }}
                        @endif
                    </p>
                @endif
                @if ($profilPerusahaan && ($profilPerusahaan->telegram || $profilPerusahaan->no_wa))
                    <p>
                        @if ($profilPerusahaan->telegram)
                            Telegram: {{ $profilPerusahaan->telegram }}
                        @endif
                        @if ($profilPerusahaan->no_wa)
                            | WhatsApp: +62{{ $profilPerusahaan->no_wa }}
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Invoice</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

    <script>
        // Auto print when page loadslogo
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>
