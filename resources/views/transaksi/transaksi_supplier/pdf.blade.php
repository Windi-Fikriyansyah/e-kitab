<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-family: sans-serif;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
        }
    </style>
</head>

<body>

    <h3>LAPORAN TRANSAKSI SUPPLIER</h3>

    @foreach ($transaksi as $t)
        <!-- ================= HEADER TRANSAKSI ================= -->
        <div class="section-title">Data Transaksi</div>
        <table>
            <tr>
                <th width="30%">Kode Transaksi</th>
                <td>{{ $t->kode_transaksi }}</td>
            </tr>
            <tr>
                <th>Nama Supplier</th>
                <td>{{ $t->nama_supplier }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Resi</th>
                <td>Rp {{ number_format($t->resi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Fee</th>
                <td>Rp {{ number_format($t->fee, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- ================= DETAIL PRODUK ================= -->
        <div class="section-title">Detail Produk</div>
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                @endphp

                @foreach ($detail->where('id_transaksi', $t->id) as $d)
                    @php $grandTotal += $d->total; @endphp

                    <tr>
                        <td>{{ $d->judul }}</td>
                        <td>Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                        <td>{{ $d->qty }}</td>
                        <td>Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr>
                    <th colspan="3" style="text-align:right;">Grand Total</th>
                    <th>Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>
            </tbody>
        </table>
    @endforeach

</body>

</html>
