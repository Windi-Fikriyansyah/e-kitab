@extends('template.app')
@section('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($customer) ? 'Edit Customer' : 'Tambah Customer' }}</h3>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <ul class="mb-0 text-white">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('message'))
            <div
                class="alert alert-{{ session('message_type') ?? 'success' }} border-0 bg-{{ session('message_type') ?? 'success' }} alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-check-circle'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">{{ session('message_title') ?? 'Success' }}</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form id="customerForm"
                    action="{{ isset($customer) ? route('kelola_data.customer.update', Crypt::encrypt($customer->id)) : route('kelola_data.customer.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($customer))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="nama">Nama Customer</label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="{{ old('nama', $customer->nama ?? '') }}" placeholder="Masukkan nama customer"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_hp">Nomor HP</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control"
                                        value="{{ old('no_hp', $customer->no_hp ?? '') }}" placeholder="81234567890"
                                        required>
                                </div>
                                <small class="text-muted">Contoh: 81234567890 (tanpa +62, 0, atau tanda baca)</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"
                                    required>{{ old('alamat', $customer->alamat ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_data.customer.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($customer) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Format WhatsApp number input
            $('#no_hp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Validate form before submit
            $('#customerForm').on('submit', function(e) {
                const noHp = $('#no_hp').val();
                if (noHp && noHp.startsWith('0')) {
                    e.preventDefault();
                    alert(
                        'Nomor HP tidak boleh dimulai dengan 0. Gunakan format contoh: 81234567890'
                    );
                    $('#no_hp').focus();
                }
            });
        });
    </script>
@endpush
