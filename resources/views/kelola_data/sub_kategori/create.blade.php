@extends('template.app')
@section('title', isset($sub_kategori) ? 'Edit Sub Kategori' : 'Tambah Sub Kategori')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($sub_kategori) ? 'Edit Sub Kategori' : 'Tambah Sub Kategori' }}</h3>
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
                <form id="sub_kategoriForm"
                    action="{{ isset($sub_kategori) ? route('kelola_data.sub_kategori.update', Crypt::encrypt($sub_kategori->id)) : route('kelola_data.sub_kategori.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($sub_kategori))
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="id_kategori">Kategori</label>
                                <select class="form-select @error('id_kategori') is-invalid @enderror" name="id_kategori"
                                    id="kategori" style="width: 100%">
                                    @if (isset($sub_kategori) && $sub_kategori->id_kategori)
                                        @php
                                            $kategori = DB::table('kategori')->find($sub_kategori->id_kategori);
                                        @endphp
                                        <option value="{{ $sub_kategori->id_kategori }}" selected>
                                            {{ $kategori->nama_arab }} | {{ $kategori->nama_indonesia }}
                                        </option>
                                    @endif
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_arab">Nama Sub Kategori (Arab)</label>
                                <input type="text" name="nama_arab" id="nama_arab" class="form-control"
                                    value="{{ old('nama_arab', $sub_kategori->nama_arab ?? '') }}"
                                    placeholder="Masukkan nama sub kategori dalam bahasa Arab" required dir="rtl"
                                    style="text-align: right;">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_indonesia">Nama Sub Kategori (Indonesia)</label>
                                <input type="text" name="nama_indonesia" id="nama_indonesia" class="form-control"
                                    value="{{ old('nama_indonesia', $sub_kategori->nama_indonesia ?? '') }}"
                                    placeholder="Masukkan nama sub kategori dalam bahasa Indonesia" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_data.sub_kategori.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($sub_kategori) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
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
            // Initialize Select2 for kategori
            $('#kategori').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Kategori",
                allowClear: true,
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.sub_kategori.getkategori') }}",
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
                                    text: item.text
                                }
                            })
                        };
                    },
                    cache: true
                },
            });


        });
    </script>
@endpush
