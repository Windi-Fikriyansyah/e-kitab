@extends('template.app')
@section('title', isset($ukuran) ? 'Edit Ukuran' : 'Tambah Ukuran')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ isset($ukuran) ? 'Edit Ukuran' : 'Tambah Ukuran' }}</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('kelola_data.ukuran.index') }}">Data Ukuran</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ isset($ukuran) ? 'Edit' : 'Tambah' }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                    <div class="d-flex align-items-center">
                        <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                        <div class="ms-3">
                            <h6 class="mb-1 text-white">Error Validasi</h6>
                            <ul class="mb-0 text-white">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                        aria-label="Close"></button>
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
                <div class="card-header">
                    <h4 class="card-title">Form Ukuran</h4>
                </div>
                <div class="card-body">
                    <form id="ukuranForm"
                        action="{{ isset($ukuran) ? route('kelola_data.ukuran.update', Crypt::encrypt($ukuran->id)) : route('kelola_data.ukuran.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($ukuran))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="ukuran" class="form-label">Nama Ukuran</label>
                                    <input type="text" name="ukuran" id="ukuran" class="form-control"
                                        value="{{ old('ukuran', $ukuran->ukuran ?? '') }}" required>
                                    <div class="form-text">Masukkan nama ukuran produk</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('kelola_data.ukuran.index') }}" class="btn btn-light-secondary">
                                <i class="bx bx-arrow-back"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> {{ isset($ukuran) ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
