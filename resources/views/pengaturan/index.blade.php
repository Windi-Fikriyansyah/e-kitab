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
                                <h6 class="mb-3 text-primary mt-4">Gambar Slider</h6>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Gambar 1</label>
                                    <div class="col-sm-10">
                                        <input type="file" name="hero_image_1"
                                            class="form-control image-upload @error('hero_image_1') is-invalid @enderror"
                                            accept="image/*" data-preview="hero_image_1_preview" />
                                        <div class="image-preview mt-2" id="hero_image_1_preview">
                                            @if (isset($landing) && $landing->hero_image_1)
                                                <img src="{{ asset('storage/' . $landing->hero_image_1) }}"
                                                    class="img-thumbnail" style="max-height: 150px;">
                                                <small class="text-muted d-block">Current:
                                                    {{ basename($landing->hero_image_1) }}</small>
                                            @endif
                                        </div>
                                        @error('hero_image_1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Gambar 2</label>
                                    <div class="col-sm-10">
                                        <input type="file" name="hero_image_2"
                                            class="form-control image-upload @error('hero_image_2') is-invalid @enderror"
                                            accept="image/*" data-preview="hero_image_2_preview" />
                                        <div class="image-preview mt-2" id="hero_image_2_preview">
                                            @if (isset($landing) && $landing->hero_image_2)
                                                <img src="{{ asset('storage/' . $landing->hero_image_2) }}"
                                                    class="img-thumbnail" style="max-height: 150px;">
                                                <small class="text-muted d-block">Current:
                                                    {{ basename($landing->hero_image_2) }}</small>
                                            @endif
                                        </div>
                                        @error('hero_image_2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
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
        $(document).ready(function() {
            // Image preview functionality
            function readURL(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#' + previewId).html(
                            '<img src="' + e.target.result +
                            '" class="img-thumbnail" style="max-height: 150px;">' +
                            '<button type="button" class="btn btn-sm btn-danger remove-preview mt-2" data-preview="' +
                            previewId + '">' +
                            '<i class="fas fa-times"></i> Hapus Preview' +
                            '</button>'
                        );
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Handle image upload and preview
            $('.image-upload').change(function() {
                var previewId = $(this).data('preview');
                readURL(this, previewId);
            });

            // Remove preview image
            $(document).on('click', '.remove-preview', function() {
                var previewId = $(this).data('preview');
                $('#' + previewId).html('');
                $('input[data-preview="' + previewId + '"]').val('');
            });
        });

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
    </script>
@endpush
