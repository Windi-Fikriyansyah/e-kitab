@extends('template.app')
@section('title', isset($article) ? 'Edit Artikel' : 'Tambah Artikel')

@section('content')
    <div class="page-heading">
        <h3>{{ isset($article) ? 'Edit Artikel' : 'Tambah Artikel' }}</h3>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($article)
                        ? route('pengaturan_web.blog.update', Crypt::encrypt($article->id))
                        : route('pengaturan_web.blog.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($article))
                        @method('PUT')
                    @endif

                    <div class="row">
                        {{-- Title --}}
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">Judul Artikel</label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $article->title ?? '') }}"
                                placeholder="Masukkan judul artikel" required>
                        </div>

                        {{-- Category & Author --}}
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <input type="text" name="category" id="category" class="form-control"
                                value="{{ old('category', $article->category ?? '') }}"
                                placeholder="Contoh: Edukasi, Tips, News">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="author" class="form-label">Penulis</label>
                            <input type="text" name="author" id="author" class="form-control"
                                value="{{ old('author', $article->author ?? 'Admin') }}"
                                placeholder="Nama penulis">
                        </div>

                        {{-- Read Time & Featured --}}
                        <div class="col-md-6 mb-3">
                            <label for="read_time" class="form-label">Estimasi Waktu Baca (menit)</label>
                            <input type="text" name="read_time" id="read_time" class="form-control"
                                value="{{ old('read_time', $article->read_time ?? '') }}"
                                placeholder="Contoh: 5 menit">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" 
                                    {{ old('is_featured', $article->is_featured ?? 0) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Tampilkan di Featured</label>
                            </div>
                        </div>

                        {{-- Excerpt --}}
                        <div class="col-md-12 mb-3">
                            <label for="excerpt" class="form-label">Ringkasan (Excerpt)</label>
                            <textarea name="excerpt" id="excerpt" class="form-control" rows="2" placeholder="Ringkasan singkat artikel">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                        </div>

                        {{-- Content --}}
                        <div class="col-md-12 mb-3">
                            <label for="content" class="form-label">Konten Artikel</label>
                            <textarea name="content" id="editor" class="form-control">{{ old('content', $article->content ?? '') }}</textarea>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-12 mb-3">
                            <label for="image" class="form-label">Gambar Unggulan (WebP)</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/webp">
                            @if (isset($article) && $article->image)
                                @php
                                    $imageData = json_decode($article->image);
                                    $url = is_object($imageData) ? $imageData->url : asset('storage/blog/' . $article->image);
                                @endphp
                                <div class="mt-2">
                                    <img src="{{ $url }}" style="max-width: 200px; border-radius: 8px;" alt="Current Image">
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pengaturan_web.blog.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($article) ? 'Update Artikel' : 'Simpan Artikel' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush

