<table>
    <tr>
        <td colspan="7"><strong>LAPORAN SUPPLIER</strong></td>
    </tr>
    <tr>
        <td><strong>Periode</strong></td>
        <td colspan="6">{{ $meta['tanggal_awal'] }} s/d {{ $meta['tanggal_akhir'] }}</td>
    </tr>
    <tr>
        <td><strong>Supplier</strong></td>
        <td colspan="6">{{ $meta['supplier_label'] }}</td>
    </tr>
    <tr>
        <td><strong>Produk</strong></td>
        <td colspan="6">{{ $meta['produk_label'] }}</td>
    </tr>
</table>

<br>

{{-- TABEL 1: TRANSAKSI --}}
<table border="1">
    <thead>
        <tr>
            <th colspan="7"><strong>DATA TRANSAKSI</strong></th>
        </tr>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Supplier</th>
            <th>Harga Beli</th>
            <th>QTY Terjual</th>
            <th>Total Terjual</th>
            <th>Tanggal Transaksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rowsTransaksi as $r)
            <tr>
                <td>{{ $r->kd_produk }}</td>
                <td>{{ $r->judul }}</td>
                <td>{{ $r->nama_supplier }}</td>
                <td>{{ $r->harga_modal }}</td>
                <td>{{ $r->qty_terjual }}</td>
                <td>{{ $r->total_terjual }}</td>
                <td>{{ $r->tanggal_transaksi }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4"><strong>TOTAL</strong></td>
            <td><strong>{{ $meta['total_qty'] }}</strong></td>
            <td><strong>{{ $meta['total_nilai'] }}</strong></td>
            <td></td>
        </tr>
    </tbody>
</table>

<br><br>

{{-- TABEL 2: TRANSAKSI SUPPLIER (FEE + RESI) --}}
<table border="1">
    <thead>
        <tr>
            <th colspan="5"><strong>DATA TRANSAKSI SUPPLIER (FEE + RESI)</strong></th>
        </tr>
        <tr>
            <th>ID Transaksi Supplier</th>
            <th>Tanggal</th>
            <th>Resi</th>
            <th>Fee</th>
            <th>Total Fee+Resi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rowsTransaksiSupplier as $x)
            <tr>
                <td>{{ $x->nama_supplier }}</td>
                <td>{{ $x->tanggal_transaksi }}</td>
                <td>{{ $x->resi }}</td>
                <td>{{ $x->fee }}</td>
                <td>{{ $x->total_fee_resi }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4"><strong>TOTAL FEE+RESI</strong></td>
            <td><strong>{{ $meta['total_fee_resi'] }}</strong></td>
        </tr>
        <tr>
            <td colspan="4"><strong>TOTAL TAGIHAN (Total Nilai - Fee - Resi)</strong></td>
            <td><strong>{{ $meta['total_tagihan'] }}</strong></td>
        </tr>
    </tbody>
</table>
