@extends('template.app')
@section('title', 'Tambah BPKB')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h2>
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
                <form id="productForm" action="{{ isset($product) ? route('kelola_data.products.update', $product->id) : route('kelola_data.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($product))
                        @method('POST')
                    @endif

                    <div class="form-group">
                        <label for="barcode">Barcode</label>
                        <input type="text" name="barcode" id="barcode" class="form-control" value="{{ old('barcode', $product->barcode ?? '') }}" placeholder="Scan atau ketik barcode">
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Produk</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" placeholder="Nama produk">
                    </div>

                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" name="category" id="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" placeholder="Kategori produk">
                    </div>

                    <div class="form-group">
                        <label for="purchase_price_display">Harga Beli</label>
                        <input type="text" id="purchase_price_display" class="form-control" value="{{ old('purchase_price', isset($product) ? number_format($product->purchase_price, 0, ',', '.') : '') }}" placeholder="0">
                        <input type="hidden" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="selling_price_display">Harga Jual/Satuan</label>
                        <input type="text" id="selling_price_display" class="form-control" value="{{ old('selling_price', isset($product) ? number_format($product->selling_price, 0, ',', '.') : '') }}" placeholder="0">
                        <input type="hidden" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stok</label>
                        <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" name="satuan" id="satuan" class="form-control" value="{{ old('satuan', $product->satuan ?? '') }}" placeholder="Satuan produk">
                    </div>

                    <div class="form-group">
                        <label for="photo">Foto Produk</label>
                        <input type="file" name="photo" id="photo" class="form-control">
                        @if(isset($product) && $product->photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="Foto Produk" width="150">
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.products.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Function to format number with thousand separator
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Function to clean number format (remove separators)
            function cleanNumber(number) {
                return number.replace(/\./g, '');
            }

            // Handle purchase price input
            $('#purchase_price_display').on('input', function() {
                let value = cleanNumber($(this).val());
                if (value !== '') {
                    // Update hidden input with clean number
                    $('#purchase_price').val(value);
                    // Update display input with formatted number
                    $(this).val(formatNumber(value));
                } else {
                    $('#purchase_price').val('');
                    $(this).val('');
                }
            });

            // Handle selling price input
            $('#selling_price_display').on('input', function() {
                let value = cleanNumber($(this).val());
                if (value !== '') {
                    // Update hidden input with clean number
                    $('#selling_price').val(value);
                    // Update display input with formatted number
                    $(this).val(formatNumber(value));
                } else {
                    $('#selling_price').val('');
                    $(this).val('');
                }
            });

            // Only allow numbers and dots in price inputs
            $('.form-control[id$="_display"]').on('keypress', function(e) {
                if (!/[\d.]/.test(String.fromCharCode(e.which))) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
