@extends('template.app')
@section('title', 'Detail Produk')
@section('content')
    <div class="page-heading">
        <h3>Detail Produk</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Produk</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Data Arab:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Kode Produk</th>
                                <td>{{ $produk->kd_produk }}</td>
                            </tr>
                            <tr>
                                <th>الفنون</th>
                                <td>
                                    @php
                                        $kategoriArab = json_decode($produk->kategori, true);
                                    @endphp

                                    @if (is_array($kategoriArab))
                                        @foreach ($kategoriArab as $kat)
                                            <span class="badge bg-primary"
                                                style="font-size:14px;">{{ $kat }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-primary">{{ $produk->kategori }}</span>
                                    @endif
                                </td>

                            </tr>
                            <tr>
                                <th>العنوان</th>
                                <td>{{ $produk->judul }}</td>
                            </tr>
                            <tr>
                                <th>المؤلف</th>
                                <td>{{ $produk->penulis }}</td>
                            </tr>
                            <tr>
                                <th>التحقيق والتعليق والاعتناء والتقديم</th>
                                <td>{{ $produk->editor }}</td>
                            </tr>
                            <tr>
                                <th>الناشر</th>
                                <td>{{ $produk->penerbit }}</td>
                            </tr>
                            <tr>
                                <th>التجليد</th>
                                <td>{{ $produk->cover }}</td>
                            </tr>
                            <tr>
                                <th>الورق</th>
                                <td>{{ $produk->kertas }}</td>
                            </tr>
                            <tr>
                                <th>الجودة</th>
                                <td>{{ $produk->kualitas }}</td>
                            </tr>
                            <tr>
                                <th>التشكيل</th>
                                <td>{{ $produk->harakat }}</td>
                            </tr>

                            {{-- Tampilkan kolom dinamis untuk data Arab --}}
                            @if (isset($dynamicColumns))
                                @foreach ($dynamicColumns as $column)
                                    @if (property_exists($produk, $column) && !is_null($produk->$column))
                                        <tr>
                                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                            <td>{{ $produk->$column }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Data Indonesia:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Judul</th>
                                <td>{{ $produk->judul_indo }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>
                                    @php
                                        $kategoriIndo = json_decode($produk->kategori_indo, true);
                                    @endphp

                                    @if (is_array($kategoriIndo))
                                        @foreach ($kategoriIndo as $kat)
                                            <span class="badge bg-success"
                                                style="font-size:14px;">{{ $kat }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-success">{{ $produk->kategori_indo }}</span>
                                    @endif
                                </td>

                            </tr>
                            <tr>
                                <th>Penerbit</th>
                                <td>{{ $produk->penerbit_indo }}</td>
                            </tr>
                            <tr>
                                <th>Cover</th>
                                <td>{{ $produk->cover_indo }}</td>
                            </tr>
                            <tr>
                                <th>Kertas</th>
                                <td>{{ $produk->kertas_indo }}</td>
                            </tr>
                            <tr>
                                <th>Kualitas</th>
                                <td>{{ $produk->kualitas_indo }}</td>
                            </tr>
                            <tr>
                                <th>Harakat</th>
                                <td>{{ $produk->harakat_indo }}</td>
                            </tr>

                            <tr>
                                <th>Penulis</th>
                                <td>{{ $produk->penulis_indo }}</td>
                            </tr>
                            {{-- Tampilkan kolom dinamis untuk data Indonesia --}}
                            @if (isset($dynamicColumns))
                                @foreach ($dynamicColumns as $column)
                                    @php
                                        $indoColumn = $column . '_indo';
                                    @endphp
                                    @if (property_exists($produk, $indoColumn) && !is_null($produk->$indoColumn))
                                        <tr>
                                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                            <td>{{ $produk->$indoColumn }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Informasi Tambahan:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="20%">Supplier</th>
                                <td>{{ $produk->nama_supplier }} ({{ $produk->telepon }})</td>
                            </tr>
                            <tr>
                                <th>Halaman</th>
                                <td>{{ $produk->halaman }}</td>
                            </tr>
                            <tr>
                                <th>Berat</th>
                                <td>{{ $produk->berat }}</td>
                            </tr>
                            <tr>
                                <th>Ukuran</th>
                                <td>{{ $produk->ukuran }}</td>
                            </tr>
                            <tr>
                                <th>Harga Modal</th>
                                <td>Rp {{ number_format($produk->harga_modal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Harga Jual</th>
                                <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>{{ $produk->stok }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('kelola_data.produk.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
