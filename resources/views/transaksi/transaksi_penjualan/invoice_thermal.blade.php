<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk #{{ $transaksi->kode_transaksi }}</title>
    <style>
        /* Set lebar 80mm; untuk 58mm ganti jadi 58mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: monospace;
            font-size: 11px;
            color: #000;
        }

        .receipt {
            width: 80mm;
            padding: 8px 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .title {
            font-weight: bold;
            font-size: 12px;
        }

        .hr {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 6px;
        }

        .small {
            font-size: 10px;
        }

        .tot {
            font-weight: bold;
        }

        .wrap {
            white-space: pre-wrap;
        }

        .item {
            margin-bottom: 4px;
        }

        .item .name {
            width: 100%;
        }

        .item .qty {
            min-width: 70px;
            text-align: right;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .btns {
            padding: 8px;
            text-align: center;
        }

        .btns button {
            padding: 6px 10px;
            margin: 0 4px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="center">
            <div class="title">{{ $profilPerusahaan->nama_latin ?? 'DAR IBNU ABBAS' }}</div>
            <div class="small">{{ $profilPerusahaan->alamat ?? '' }}</div>
            <div class="small">Tel: {{ $profilPerusahaan->no_telepon ?? '' }}</div>
        </div>

        <div class="hr"></div>

        <div class="small">
            No: {{ $transaksi->nomor_urut ?? '-' }}<br>
            Tgl: {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i') }}<br>
            Kasir: {{ $transaksi->kasir }}
        </div>

        @if (!empty($transaksi->nama_customer))
            <div class="small">Cust:
                {{ $transaksi->nama_customer }}{{ $transaksi->no_hp_customer ? ' / ' . $transaksi->no_hp_customer : '' }}
            </div>
        @endif

        <div class="hr"></div>

        {{-- Daftar Item --}}
        @foreach ($items as $it)
            <div class="item">
                <div class="name wrap">{{ $it->judul }}</div>
                <div class="row">
                    <div class="left small">{{ $it->kd_produk }}</div>
                    <div class="qty small">{{ $it->quantity }} x {{ number_format($it->unit_price, 0, ',', '.') }}
                    </div>
                </div>
                <div class="right">{{ number_format($it->total_price, 0, ',', '.') }}</div>
            </div>
        @endforeach

        <div class="hr"></div>

        {{-- Ringkasan --}}
        <div class="row">
            <div>Subtotal</div>
            <div>{{ number_format($subtotal, 0, ',', '.') }}</div>
        </div>
        @if ($diskon > 0)
            <div class="row">
                <div>Diskon</div>
                <div>-{{ number_format($diskon, 0, ',', '.') }}</div>
            </div>
        @endif
        @if ($potongan > 0)
            <div class="row">
                <div>Potongan</div>
                <div>-{{ number_format($potongan, 0, ',', '.') }}</div>
            </div>
        @endif
        @if ($ongkir > 0)
            <div class="row">
                <div>Ongkir</div>
                <div>{{ number_format($ongkir, 0, ',', '.') }}</div>
            </div>
        @endif
        @if ($packing > 0)
            <div class="row">
                <div>Packing Kayu</div>
                <div>{{ number_format($packing, 0, ',', '.') }}</div>
            </div>
        @endif

        <div class="row tot">
            <div>Grand Total</div>
            <div>{{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>

        <div class="hr"></div>

        <div class="row">
            <div>Bayar ({{ strtoupper($transaksi->payment_method) }})</div>
            <div>{{ number_format($dibayar, 0, ',', '.') }}</div>
        </div>

        @if ($kembali > 0)
            <div class="row">
                <div>Kembali</div>
                <div>{{ number_format($kembali, 0, ',', '.') }}</div>
            </div>
        @endif
        @if ($sisa > 0)
            <div class="row">
                <div>Sisa</div>
                <div>{{ number_format($sisa, 0, ',', '.') }}</div>
            </div>
        @endif

        @if (!empty($transaksi->notes))
            <div class="hr"></div>
            <div class="small wrap"><b>Catatan:</b> {{ $transaksi->notes }}</div>
        @endif

        @if (!empty($transaksi->ekspedisi))
            <div class="hr"></div>
            <div class="small">Ekspedisi: {{ $transaksi->ekspedisi }}</div>
        @endif

        <div class="hr"></div>

        <div class="footer small">
            Terima kasih 🙏<br>
            Barang yang sudah dibeli tidak dapat ditukar/kembali kecuali cacat produksi.
        </div>
    </div>

    <div class="no-print btns">
        <button onclick="window.print()">Cetak Thermal</button>
    </div>
</body>

</html>
