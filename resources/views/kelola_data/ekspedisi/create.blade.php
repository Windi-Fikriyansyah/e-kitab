@extends('template.app')
@section('title', isset($ekspedisi) ? 'Edit Ekspedisi' : 'Tambah Ekspedisi')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($ekspedisi) ? 'Edit Ekspedisi' : 'Tambah Ekspedisi' }}</h3>
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
                <form id="ekspedisiForm"
                    action="{{ isset($ekspedisi) ? route('kelola_data.ekspedisi.update', Crypt::encrypt($ekspedisi->id)) : route('kelola_data.ekspedisi.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($ekspedisi))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_ekspedisi">Nama Ekspedisi</label>
                                <input type="text" name="nama_ekspedisi" id="nama_ekspedisi" class="form-control"
                                    value="{{ old('nama_ekspedisi', $ekspedisi->nama_ekspedisi ?? '') }}"
                                    placeholder="Masukkan nama ekspedisi" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="ekspedisi_logo">Logo Ekspedisi</label>
                                <input type="file" name="ekspedisi_logo" id="ekspedisi_logo" class="form-control">
                                @if (isset($ekspedisi) && $ekspedisi->ekspedisi_logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $ekspedisi->ekspedisi_logo) }}" alt="Logo Ekspedisi"
                                            style="max-width: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_data.ekspedisi.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($ekspedisi) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
