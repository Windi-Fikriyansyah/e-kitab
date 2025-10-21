<!DOCTYPE html>
<html>

<head>
    <title>Laporan Riwayat Produk</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/dejavu-sans.ttf') }}) format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .info {
            margin-bottom: 20px;
        }

        .product-name {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN RIWAYAT PRODUK</h2>
    </div>

    <div class="info">
        <p><strong>Nama Produk:</strong> <span class="product-name">{{ $produk->judul ?? '-' }}</span></p>
        <p><strong>Periode:</strong> {{ $tanggal_awal ?? '-' }} s/d {{ $tanggal_akhir ?? '-' }}</p>
        <p><strong>Filter Tipe:</strong> {{ $filter_type ?? 'Semua' }}</p>
        <p><strong>Filter User:</strong> {{ $filter_user ?? 'Semua' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Tipe</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($riwayat as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                    <td>{{ date('H:i:s', strtotime($item->created_at)) }}</td>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->user }}</td>
                    <td>{{ $item->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
