@extends('template.app')
@section('title', 'Pengaturan Landing Page - Hero Section')
@section('content')
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
            @if (session('success'))
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="successToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="errorToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" id="validationToast">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Terdapat kesalahan dalam pengisian form. Silakan periksa kembali.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($landing) ? 'Edit' : 'Tambah' }} Pengaturan Hero Section</h5>
                        <small class="text-muted float-end">Kelola konten hero section halaman utama</small>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($landing) ? route('pengaturan_web.hero.update', $landing->id) : route('pengaturan_web.hero.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($landing))
                                @method('PUT')
                            @endif

                            <div class="mt-4">
                                <!-- Gambar Slider -->
                                <h6 class="mb-3 text-primary mt-4">Gambar Slider (Hero)</h6>

                                <div id="heroImagesContainer">
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Tambah Gambar</label>
                                        <div class="col-sm-10">
                                            <div class="input-group mb-3">
                                                <input type="file" name="hero_images[]" class="form-control" accept="image/webp,image/*" multiple>
                                                <button type="button" class="btn btn-success" onclick="addHeroImageField()">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (isset($landing))
                                    @php
                                        $images = [];
                                        if ($landing->hero_images) {
                                            $images = json_decode($landing->hero_images);
                                        } else {
                                            if ($landing->hero_image_1) $images[] = (object)['url' => asset('storage/'.$landing->hero_image_1), 'path' => $landing->hero_image_1];
                                            if ($landing->hero_image_2) $images[] = (object)['url' => asset('storage/'.$landing->hero_image_2), 'path' => $landing->hero_image_2];
                                        }
                                    @endphp

                                    @if(count($images) > 0)
                                        <h6 class="mb-3 text-primary mt-4">Gambar Saat Ini</h6>
                                        <div class="row">
                                            @foreach ($images as $index => $image)
                                                @php
                                                    $valToDelete = isset($image->file_id) ? $image->file_id : ($image->path ?? $image->url);
                                                @endphp
                                                <div class="col-md-3 mb-3">
                                                    <div class="card h-100 border">
                                                        <img src="{{ $image->url }}" class="card-img-top p-2" style="height: 150px; object-fit: cover;">
                                                        <div class="card-body p-2 text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $valToDelete }}" id="delHero-{{ $index }}">
                                                                <label class="form-check-label ms-2" for="delHero-{{ $index }}">Hapus</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row justify-content-end mt-4">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($landing) ? 'Update' : 'Simpan' }} Pengaturan
                                    </button>
                                    <a href="{{ route('pengaturan_web.hero.index') }}"
                                        class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .text-primary {
            color: #007bff !important;
        }

        .image-preview {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .image-preview img {
            max-height: 150px;
            margin-bottom: 5px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .toast {
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .toast-body {
            padding: 0.75rem;
        }
    </style>
@endpush

@push('js')
    <script>
        function addHeroImageField() {
            const container = document.getElementById('heroImagesContainer');
            const newField = document.createElement('div');
            newField.className = 'row mb-3';
            newField.innerHTML = `
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <div class="input-group mb-3">
                        <input type="file" name="hero_images[]" class="form-control" accept="image/webp,image/*">
                        <button type="button" class="btn btn-danger" onclick="removeHeroImageField(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newField);
        }

        function removeHeroImageField(button) {
            button.closest('.row').remove();
        }

        $(document).ready(function() {
            // Toast initialization
            @if (session('success'))
                var successToast = new bootstrap.Toast(document.getElementById('successToast'));
                successToast.show();
            @endif

            @if (session('error'))
                var errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
                errorToast.show();
            @endif

            @if ($errors->any())
                var validationToast = new bootstrap.Toast(document.getElementById('validationToast'));
                validationToast.show();
            @endif
        });
    </script>
@endpush
