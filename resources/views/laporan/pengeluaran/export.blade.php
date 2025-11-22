<table border="1">
    <thead>
        <tr>
            <th colspan="6" style="text-align:center; font-weight:bold; font-size:16px;">
                LAPORAN PENGELUARAN
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align:center;">
                Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}
                @if ($kategori)
                    (Kategori: {{ $kategori }})
                @endif
            </th>
        </tr>
        <tr>
            <th style="background-color:#e5e5e5;">Tanggal</th>
            <th style="background-color:#e5e5e5;">Kategori</th>
            <th style="background-color:#e5e5e5;">Deskripsi</th>
            <th style="background-color:#e5e5e5;">Nominal</th>
            <th style="background-color:#e5e5e5;">Metode Bayar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $row->kategori }}</td>
                <td>{{ $row->deskripsi }}</td>
                <td>Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                <td>{{ $row->metode_bayar }}</td>
            </tr>
        @endforeach
        <tr style="font-weight:bold; background-color:#f2f2f2;">
            <td colspan="3" style="text-align:right;">TOTAL</td>
            <td>Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
