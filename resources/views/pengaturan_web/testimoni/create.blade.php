@extends('template.app')
@section('title', isset($testimoni) ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
    <div class="page-heading">
        <h3>{{ isset($testimoni) ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h3>
    </div>
    <div class="page-content">

        {{-- Error Handling --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <ul class="mb-0 text-white">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Success Message --}}
        @if (session('message'))
            <div
                class="alert alert-{{ session('message_type') ?? 'success' }} border-0 bg-{{ session('message_type') ?? 'success' }} alert-dismissible fade show">
                <div class="text-white">
                    <strong>{{ session('message_title') ?? 'Success' }}:</strong> {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($testimoni) ? route('pengaturan_web.testimoni.update', Crypt::encrypt($testimoni->id)) : route('pengaturan_web.testimoni.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($testimoni))
                        @method('PUT')
                    @endif

                    <div class="row">
                        {{-- Nama Customer --}}
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_customer">Nama Customer</label>
                                <input type="text" name="nama_customer" id="nama_customer" class="form-control"
                                    value="{{ old('nama_customer', $testimoni->nama_customer ?? '') }}"
                                    placeholder="Masukkan nama customer" required>
                            </div>
                        </div>

                        {{-- Caption --}}
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="caption">Caption</label>
                                <input type="text" name="caption" id="caption" class="form-control"
                                    value="{{ old('caption', $testimoni->caption ?? '') }}"
                                    placeholder="Masukkan caption testimoni" required>
                            </div>
                        </div>
                    </div>

                    {{-- Foto Unboxing --}}
                    <div class="form-group mb-3">
                        <label for="foto_unboxing">Foto Unboxing</label>
                        <input type="file" name="foto_unboxing" id="foto_unboxing" class="form-control"
                            {{ isset($testimoni) ? '' : 'required' }}>
                        @if (isset($testimoni) && $testimoni->foto_unboxing)
                            <div class="mt-2">
                                <img src="{{ asset('storage/testimoni/' . $testimoni->foto_unboxing) }}" class="logo-img"
                                    alt="Foto Unboxing">
                            </div>
                        @endif
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pengaturan_web.testimoni.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($testimoni) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
