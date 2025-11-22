@extends('template.app')
@section('title', isset($profile) ? 'Edit Profile Perusahaan' : 'Tambah Profile Perusahaan')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($profile) ? 'Edit Profile Perusahaan' : 'Tambah Profile Perusahaan' }}</h3>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
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
                <form id="profileForm"
                    action="{{ isset($profile) ? route('kelola_data.profile_perusahaan.update', Crypt::encrypt($profile->id)) : route('kelola_data.profile_perusahaan.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($profile))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_toko">Nama Perusahaan/Toko</label>
                                <input type="text" name="nama_toko" id="nama_toko" class="form-control"
                                    value="{{ old('nama_toko', $profile->nama_toko ?? '') }}"
                                    placeholder="Masukkan nama perusahaan/toko" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="no_wa">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="no_wa" id="no_wa" class="form-control"
                                        value="{{ old('no_wa', $profile->no_wa ?? '') }}" placeholder="81234567890"
                                        required>
                                </div>
                                <small class="text-muted">Contoh: 81234567890 (tanpa +62, 0, atau tanda baca)</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"
                                    required>{{ old('alamat', $profile->alamat ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="logo">Logo Perusahaan</label>
                                <input type="file" name="logo" id="logo" class="form-control"
                                    accept="image/jpeg,image/png,image/jpg,image/gif">
                                <small class="text-muted">Format: JPEG, PNG, JPG, GIF (Max: 2MB)</small>

                                @if (isset($profile) && $profile->logo)
                                    <div class="mt-3">
                                        <p>Logo Saat Ini:</p>
                                        <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo Perusahaan"
                                            class="img-thumbnail" width="150">
                                        {{-- <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_logo"
                                                id="remove_logo">
                                            <label class="form-check-label" for="remove_logo">
                                                Hapus logo
                                            </label>
                                        </div> --}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Social Media</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="ig">Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="ig" id="ig" class="form-control"
                                        value="{{ old('ig', $profile->ig ?? '') }}" placeholder="username">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="fb">Facebook</label>
                                <input type="text" name="fb" id="fb" class="form-control"
                                    value="{{ old('fb', $profile->fb ?? '') }}" placeholder="Link Facebook">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="telegram">Telegram</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="telegram" id="telegram" class="form-control"
                                        value="{{ old('telegram', $profile->telegram ?? '') }}" placeholder="username">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="tokopedia">Tokopedia</label>
                                <input type="text" name="tokopedia" id="tokopedia" class="form-control"
                                    value="{{ old('tokopedia', $profile->tokopedia ?? '') }}"
                                    placeholder="Link Tokopedia">
                            </div>

                            <div class="form-group mb-3">
                                <label for="shoope">Shoope</label>
                                <input type="text" name="shoope" id="shoope" class="form-control"
                                    value="{{ old('shoope', $profile->shoope ?? '') }}" placeholder="Link Shoope">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_data.profile_perusahaan.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($profile) ? 'Update' : 'Simpan' }}
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
            $('#no_wa').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Validate form before submit
            $('#profileForm').on('submit', function(e) {
                const noWa = $('#no_wa').val();
                if (noWa && noWa.startsWith('0')) {
                    e.preventDefault();
                    alert(
                        'Nomor WhatsApp tidak boleh dimulai dengan 0. Gunakan format contoh: 81234567890'
                    );
                    $('#no_wa').focus();
                }
            });

            // Preview image before upload
            $('#logo').change(function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file maksimal 2MB');
                        $(this).val('');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if ($('#logo-preview').length) {
                            $('#logo-preview').attr('src', e.target.result);
                        } else {
                            $('<div class="mt-3"><p>Preview:</p>' +
                                    '<img id="logo-preview" src="' + e.target.result +
                                    '" class="img-thumbnail" width="150"></div>')
                                .insertAfter('#logo');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
