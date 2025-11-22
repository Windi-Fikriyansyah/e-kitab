@extends('template.app')
@section('title', isset($barang_keluar) ? 'Edit Barang Keluar' : 'Tambah Barang Keluar')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($barang_keluar) ? 'Edit Barang Keluar' : 'Tambah Barang Keluar' }}</h2>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Pesan</h6>
                        <div class="text-white">{{ session('error') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form id="barang_keluarForm"
                    action="{{ isset($barang_keluar) ? route('transaksi.barang_keluar.update', Crypt::encrypt($barang_keluar->id)) : route('transaksi.barang_keluar.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($barang_keluar))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Kolom Pertama (Arab) -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id_produk">Pilih Produk</label>
                                <select class="form-select @error('id_produk') is-invalid @enderror"
                                    name="{{ isset($barang_keluar) ? 'id_produk_display' : 'id_produk' }}" dir="rtl"
                                    id="id_produk" style="width: 100%" {{ isset($barang_keluar) ? 'disabled' : '' }}>
                                    @if (isset($barang_keluar) && $barang_keluar->id_produk)
                                        <option value="{{ $barang_keluar->id_produk }}" selected>
                                            {{ $barang_keluar->produk->judul ?? '' }} |
                                            {{ $barang_keluar->produk->kategori ?? '' }} |
                                            {{ $barang_keluar->produk->supplier_relasi->nama_supplier ?? '' }}
                                        </option>
                                    @endif
                                </select>
                                @error('id_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (isset($barang_keluar))
                                    <small class="text-muted">Produk tidak dapat diubah saat mode edit</small>
                                @endif

                                @if (isset($barang_keluar))
                                    <input type="hidden" name="id_produk" value="{{ $barang_keluar->id_produk }}">
                                @endif
                            </div>
                        </div>

                        <!-- Kolom Kedua (Indonesia) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kd_produk">Kode Produk</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="kd_produk" id="kd_produk"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->kd_produk ?? '' : '' }}"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label for="penerbit">Penerbit</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="penerbit" id="penerbit"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->penerbit ?? '' : '' }}"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="penulis">Penulis</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="penulis" id="penulis"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->penulis ?? '' : '' }}"
                                    readonly>
                            </div>

                            <div class="form-group">
                                <label for="kategori">Kategori</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="kategori" id="kategori"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->kategori ?? '' : '' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="supplier" id="supplier"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->supplier_relasi->nama_supplier ?? '' : '' }}"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok_saat_ini">Stok Saat Ini</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="stok_saat_ini"
                                    id="stok_saat_ini"
                                    value="{{ isset($barang_keluar) ? $barang_keluar->produk->stok ?? '' : '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stok_keluar">Stok Keluar</label>
                        <input type="number" name="stok_keluar" id="stok_keluar" class="form-control"
                            value="{{ old('stok_keluar', $barang_keluar->stok_keluar ?? '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" class="form-control">{{ old('notes', $barang_keluar->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit"
                        class="btn btn-primary">{{ isset($barang_keluar) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('transaksi.barang_keluar.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#id_produk').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Produk",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('transaksi.barang_keluar.getproduk') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    kd_produk: item.kd_produk,
                                    penulis: item.penulis,
                                    kategori: item.kategori,
                                    penerbit: item.penerbit,
                                    supplier_nama: item.supplier_nama,
                                    stok: item.stok
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            // Ketika produk dipilih
            $('#id_produk').on('select2:select', function(e) {
                var data = e.params.data;

                // Isi semua field yang terkait
                $('#kd_produk').val(data.kd_produk);
                $('#penulis').val(data.penulis);
                $('#kategori').val(data.kategori);
                $('#penerbit').val(data.penerbit);
                $('#supplier').val(data.supplier_nama);
                $('#stok_saat_ini').val(data.stok);
            });

            @if (isset($barang_keluar) && $barang_keluar->id_produk)
                // Jika dalam mode edit, isi data produk
                $.ajax({
                    url: "{{ route('transaksi.barang_keluar.getproduk') }}",
                    type: "POST",
                    data: {
                        id: "{{ $barang_keluar->id_produk }}",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.length > 0) {
                            var product = data[0];
                            $('#kd_produk').val(product.kd_produk);
                            $('#penulis').val(product.penulis);
                            $('#kategori').val(product.kategori);
                            $('#penerbit').val(product.penerbit);
                            $('#supplier').val(product.supplier_nama);
                            $('#stok_saat_ini').val(product.stok);

                            // Update Select2 display
                            var $select = $('#id_produk');
                            var option = new Option(product.text, product.id, true, true);
                            $select.append(option).trigger('change');
                        }
                    }
                });
            @endif
        });
    </script>
@endpush
