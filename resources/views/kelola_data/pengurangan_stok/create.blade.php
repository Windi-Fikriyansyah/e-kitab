@extends('template.app')
@section('title', 'Pengeluaran Stok')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($PS) ? 'Edit Pengeluaran Stok' : 'Tambah Pengeluaran Stok' }}</h2>
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
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form id="productForm" action="{{ isset($PS) ? route('kelola_data.PenguranganStok.update', $PS->id) : route('kelola_data.PenguranganStok.store') }}" method="POST">
                    @csrf
                    @if(isset($PS))
                        @method('POST')
                    @endif

                    <div class="form-group">
                        <label for="barcode">Barcode</label>
                        <select id="barcode" name="barcode" class="form-control" placeholder="Pilih atau ketik barcode">
                            <!-- Option akan diisi melalui AJAX -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" disabled name="name" id="name" class="form-control" value="{{ old('name', $PS->name ?? '') }}" placeholder="Nama produk" readonly>
                    </div>

                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" disabled name="category" id="category" class="form-control" value="{{ old('category', $PS->category ?? '') }}" placeholder="Kategori produk" readonly>
                    </div>

                    <div class="form-group">
                        <label for="purchase_price_display">Harga Beli</label>
                        <input type="text" id="purchase_price_display" class="form-control" value="{{ old('purchase_price', isset($PS) ? number_format($PS->purchase_price, 0, ',', '.') : '') }}" placeholder="0" readonly>
                        <input type="hidden" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $PS->purchase_price ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="selling_price_display">Harga Jual</label>
                        <input type="text" id="selling_price_display" class="form-control" value="{{ old('selling_price', isset($PS) ? number_format($PS->selling_price, 0, ',', '.') : '') }}" placeholder="0" readonly>
                        <input type="hidden" name="selling_price" id="selling_price" value="{{ old('selling_price', $PS->selling_price ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" disabled name="stock" id="stock" class="form-control" value="{{ old('stock', $PS->stock ?? '') }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="kurang_stock">Jumlah Stok Keluar</label>
                        <input type="number" name="kurang_stock" id="kurang_stock" class="form-control" value="{{ old('kurang_stock', $PS->kurang_stock ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan">{{ old('keterangan', $PS->keterangan ?? '') }}</textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">{{ isset($PS) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.PenguranganStok.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi select2 untuk barcode
            $('#barcode').select2({
                placeholder: 'Pilih atau ketik barcode',
                ajax: {
                    url: '{{ route('kelola_data.PenguranganStok.search') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data.map(function (product) {
                                return {
                                    id: product.barcode,
                                    text: product.barcode + ' - ' + product.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Ketika barcode dipilih
        $('#barcode').on('select2:select', function (e) {
        var selectedData = e.params.data;
        var barcode = selectedData.id;

        // Gunakan route helper untuk URL API
        var url = '{{ route("kelola_data.PenguranganStok.getByBarcode", ":barcode") }}';
        url = url.replace(':barcode', barcode);

        // Ambil data produk berdasarkan barcode
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function (product) {
                $('#name').val(product.name);
                $('#category').val(product.category);
                $('#purchase_price_display').val(formatNumber(product.purchase_price));
                $('#selling_price_display').val(formatNumber(product.selling_price));
                $('#stock').val(product.stock);

                $('#purchase_price').val(product.purchase_price);
                $('#selling_price').val(product.selling_price);
            },
            error: function () {
                alert('Gagal mengambil data produk. Silakan coba lagi.');
            }
        });
    });

    $('#productForm').submit(function(event) {
        const stock = parseInt($('#stock').val()) || 0; // Stok yang tersedia
        const kurangStock = parseInt($('#kurang_stock').val()) || 0; // Jumlah stok yang akan dikurangi

        if (!$('#barcode').val()) {
            alert('Barcode harus dipilih!');
            event.preventDefault(); // Mencegah form disubmit
            return;
        }

        if (kurangStock > stock) {
            alert('Jumlah stok keluar tidak boleh lebih dari stok tersedia!');
            event.preventDefault(); // Mencegah form disubmit
            return;
        }
    });

            // Fungsi untuk format angka dengan separator ribuan
            function formatNumber(number) {
    // Pastikan angka dalam bentuk string dan tambahkan pemisah ribuan
    let formatted = number.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

    // Hilangkan .00 di akhir jika ada
    if (formatted.endsWith('.00')) {
        formatted = formatted.slice(0, -3);
    }

    return formatted;
}


            // Fungsi untuk membersihkan format angka
            function cleanNumber(number) {
                return number.replace(/\./g, '');
            }
        });
    </script>
@endpush
