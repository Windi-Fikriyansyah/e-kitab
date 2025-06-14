@extends('template.app')
@section('title', isset($cover) ? 'Edit Cover' : 'Tambah Cover')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($cover) ? 'Edit Cover' : 'Tambah Cover' }}</h3>
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
                <form id="coverForm"
                    action="{{ isset($cover) ? route('kelola_data.cover.update', Crypt::encrypt($cover->id)) : route('kelola_data.cover.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($cover))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3" dir="rtl">
                                <label for="nama_arab" style="text-align: right; display: block;">نوع تجليد</label>
                                <input type="text" name="nama_arab" id="nama_arab" class="form-control"
                                    value="{{ old('nama_arab', $cover->nama_arab ?? '') }}"
                                    placeholder="Masukkan nama cover dalam bahasa Arab" required dir="rtl"
                                    style="text-align: right;">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nama_indonesia">Nama cover (Indonesia)</label>
                                <input type="text" name="nama_indonesia" id="nama_indonesia" class="form-control"
                                    value="{{ old('nama_indonesia', $cover->nama_indonesia ?? '') }}"
                                    placeholder="Masukkan nama cover dalam bahasa Indonesia" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_data.cover.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($cover) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
