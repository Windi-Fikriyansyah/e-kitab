@extends('template.app')
@section('title', 'Supplier')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($supplier) ? 'Edit Supplier' : 'Tambah Supplier' }}</h2>
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
                <form id="supplierForm"
                    action="{{ isset($supplier) ? route('kelola_data.supplier.update', Crypt::encrypt($supplier->id)) : route('kelola_data.supplier.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($supplier))
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="nama_supplier">Nama Supplier</label>
                        <input type="text" name="nama_supplier" id="nama_supplier" class="form-control"
                            value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}" placeholder="Nama supplier">
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" placeholder="Alamat supplier">{{ old('alamat', $supplier->alamat ?? '') }}</textarea>
                    </div>




                    <div class="form-group">
                        <label for="telepon">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text">+62</span>
                            <input type="text" name="telepon" id="telepon" class="form-control"
                                value="{{ old('telepon', $supplier->telepon ?? '') }}" placeholder="81234567890" required>
                        </div>
                        <small class="text-muted">Contoh: 81234567890 (tanpa +62, 0, atau tanda baca)</small>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $supplier->email ?? '') }}" placeholder="Alamat email">
                    </div>

                    <button type="submit" class="btn btn-primary">{{ isset($supplier) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.supplier.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('#telepon').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#supplierForm').on('submit', function(e) {
            const noWa = $('#telepon').val();
            if (noWa && noWa.startsWith('0')) {
                e.preventDefault();
                alert(
                    'Nomor WhatsApp tidak boleh dimulai dengan 0. Gunakan format contoh: 81234567890'
                );
                $('#telepon').focus();
            }
        });
    </script>
@endpush
