<!DOCTYPE html>
<html>

<head>
    <style>
        body,
        table,
        td,
        th {
            font-family: 'Amiri';
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 5px;
        }

        th {
            background: #f0f0f0;
        }

        h3 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h3>Laporan Data Produk Lengkap</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>KD Produk</th>
                <th>Judul</th>
                <th>Cover</th>
                <th>Kertas</th>
                <th>Kualitas</th>
                <th>Harakat</th>
                <th>Halaman</th>
                <th>Berat</th>
                <th>Ukuran</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Penerbit</th>
                <th>Supplier</th>
                <th>Penulis</th>
                <th>Harga Modal</th>
                <th>Harga Jual</th>
                <th>Laba</th>
                <th>Stok</th>
                <th>Images</th>
                <th>Link YouTube</th>
                <th>Editor</th>
                <th>Deskripsi</th>
                <th>Created</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produk as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->kd_produk }}</td>
                    <td>{{ $p->judul }}</td>
                    <td>{{ $p->cover }}</td>
                    <td>{{ $p->kertas }}</td>
                    <td>{{ $p->kualitas }}</td>
                    <td>{{ $p->harakat }}</td>
                    <td>{{ $p->halaman }}</td>
                    <td>{{ $p->berat }}</td>
                    <td>{{ $p->ukuran }}</td>
                    <td>{{ $p->kategori }}</td>
                    <td>{{ $p->sub_kategori }}</td>
                    <td>{{ $p->penerbit }}</td>
                    <td>{{ $p->supplier }}</td>
                    <td>{{ $p->penulis }}</td>
                    <td>{{ $p->harga_modal }}</td>
                    <td>{{ $p->harga_jual }}</td>
                    <td>{{ $p->laba }}</td>
                    <td>{{ $p->stok }}</td>
                    <td>{{ $p->images }}</td>
                    <td>{{ $p->link_youtube }}</td>
                    <td>{{ $p->editor }}</td>
                    <td>{{ strip_tags($p->deskripsi) }}</td>
                    <td>{{ $p->created_at }}</td>
                    <td>{{ $p->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
