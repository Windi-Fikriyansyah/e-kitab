@extends('template.app')
@section('title', isset($deskripsi) ? 'Edit Format Deskripsi' : 'Tambah Format Deskripsi')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($deskripsi) ? 'Edit Format Deskripsi' : 'Tambah Format Deskripsi' }}</h3>
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
                <form id="deskripsiForm"
                    action="{{ isset($deskripsi) ? route('kelola_link.deskripsi.update', Crypt::encrypt($deskripsi->id)) : route('kelola_link.deskripsi.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($deskripsi))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="nama_format">Nama Format</label>
                                <input type="text" name="nama_format" id="nama_format" class="form-control"
                                    value="{{ old('nama_format', $deskripsi->nama_format ?? '') }}"
                                    placeholder="Masukkan nama format" required>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Platform</label>
                            <div class="form-group">
                                <select name="platform" id="platform" class="form-select" required>
                                    <option value="">Pilih Platform</option>
                                    <option value="whatsapp"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'whatsapp' ? 'selected' : (old('platform') == 'whatsapp' ? 'selected' : '') }}>
                                        WhatsApp</option>
                                    <option value="instagram"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'instagram' ? 'selected' : (old('platform') == 'instagram' ? 'selected' : '') }}>
                                        Instagram</option>
                                    <option value="facebook"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'facebook' ? 'selected' : (old('platform') == 'facebook' ? 'selected' : '') }}>
                                        Facebook</option>
                                    <option value="threads"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'threads' ? 'selected' : (old('platform') == 'threads' ? 'selected' : '') }}>
                                        Threads</option>
                                    <option value="x"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'x' ? 'selected' : (old('platform') == 'x' ? 'selected' : '') }}>
                                        X</option>
                                    <option value="pinterest"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'pinterest' ? 'selected' : (old('platform') == 'pinterest' ? 'selected' : '') }}>
                                        Pinterest</option>
                                    <option value="marketplace"
                                        {{ isset($deskripsi) && $deskripsi->platform == 'marketplace' ? 'selected' : (old('platform') == 'marketplace' ? 'selected' : '') }}>
                                        Marketplace</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12 mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="language" id="language_indonesia"
                                    value="1"
                                    {{ (isset($deskripsi) && $deskripsi->language == 1) || old('language', 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="language_indonesia">Indonesia</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="language" id="language_arab"
                                    value="2"
                                    {{ (isset($deskripsi) && $deskripsi->language == 2) || old('language') == 2 ? 'checked' : '' }}>
                                <label class="form-check-label" for="language_arab">Arab</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3" id="indonesia_container"
                                style="{{ (isset($deskripsi) && $deskripsi->language == 2) || old('language') == 2 ? 'display: none;' : '' }}">
                                <label for="format_indonesia">Format deskripsi (Indonesia)</label>
                                <textarea name="format_indonesia" id="format_indonesia" class="form-control" style="text-align: left; direction: ltr;"
                                    placeholder="Masukkan format deskripsi dalam bahasa Indonesia">{{ old('format_indonesia', isset($deskripsi) && $deskripsi->language == 1 ? $deskripsi->format_deskripsi : '') }}</textarea>
                                <small class="text-muted">Gunakan placeholder seperti {nama_produk}, {harga}, {deskripsi}
                                    untuk menampilkan data produk</small>

                                <div class="mt-2">
                                    <label>Kolom Produk yang Tersedia:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($productColumns as $column)
                                            <button type="button" class="btn btn-sm btn-outline-primary insert-column"
                                                data-column="{{ $column }}">{{ $column }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3" id="arab_container"
                                style="{{ !isset($deskripsi) || (isset($deskripsi) && $deskripsi->language == 1) || old('language') != 2 ? 'display: none;' : '' }}">
                                <label for="format_arab">Format deskripsi (Arab)</label>
                                <textarea name="format_arab" id="format_arab" class="form-control" style="text-align: right; direction: rtl;"
                                    placeholder="Masukkan format deskripsi dalam bahasa Arab">{{ old('format_arab', isset($deskripsi) && $deskripsi->language == 2 ? $deskripsi->format_deskripsi : '') }}</textarea>
                                <small class="text-muted">Gunakan placeholder seperti {nama_produk}, {harga}, {deskripsi}
                                    untuk menampilkan data produk</small>

                                <div class="mt-2">
                                    <label>Kolom Produk yang Tersedia:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($productColumns as $column)
                                            <button type="button" class="btn btn-sm btn-outline-primary insert-column"
                                                data-column="{{ $column }}">{{ $column }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_link.deskripsi.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($deskripsi) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const indonesiaRadio = document.getElementById('language_indonesia');
            const arabRadio = document.getElementById('language_arab');
            const indonesiaContainer = document.getElementById('indonesia_container');
            const arabContainer = document.getElementById('arab_container');
            const formatIndonesia = document.getElementById('format_indonesia');
            const formatArab = document.getElementById('format_arab');

            function toggleTextareas() {
                if (indonesiaRadio.checked) {
                    indonesiaContainer.style.display = 'block';
                    arabContainer.style.display = 'none';
                    formatIndonesia.required = true;
                    formatArab.required = false;
                } else {
                    indonesiaContainer.style.display = 'none';
                    arabContainer.style.display = 'block';
                    formatIndonesia.required = false;
                    formatArab.required = true;
                }
            }

            // Initial toggle
            toggleTextareas();

            // Add event listeners
            indonesiaRadio.addEventListener('change', toggleTextareas);
            arabRadio.addEventListener('change', toggleTextareas);

            // Insert column buttons functionality
            document.querySelectorAll('.insert-column').forEach(button => {
                button.addEventListener('click', function() {
                    const columnName = this.getAttribute('data-column');
                    const placeholder = `{${columnName}}`;

                    // Determine which textarea is active
                    const activeTextarea = indonesiaRadio.checked ? formatIndonesia : formatArab;

                    // Insert at cursor position
                    const startPos = activeTextarea.selectionStart;
                    const endPos = activeTextarea.selectionEnd;
                    const currentValue = activeTextarea.value;

                    activeTextarea.value = currentValue.substring(0, startPos) + placeholder +
                        currentValue.substring(endPos);

                    // Set cursor position after inserted text
                    activeTextarea.selectionStart = activeTextarea.selectionEnd = startPos +
                        placeholder.length;
                    activeTextarea.focus();
                });
            });
        });
    </script>
@endpush
