@extends('template.app')
@section('title', isset($social) ? 'Edit Link Social Media' : 'Tambah Link Social Media')

@section('content')
    <div class="page-heading">
        <h3>{{ isset($social) ? 'Edit Link Social Media' : 'Tambah Link Social Media' }}</h3>
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
                    action="{{ isset($social)
                        ? route('pengaturan_web.link_social.update', Crypt::encrypt($social->id))
                        : route('pengaturan_web.link_social.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($social))
                        @method('PUT')
                    @endif

                    <div class="row">
                        {{-- Facebook --}}
                        <div class="col-md-6 mb-3">
                            <label for="facebook">Facebook</label>
                            <input type="url" name="facebook" id="facebook" class="form-control"
                                value="{{ old('facebook', $social->facebook ?? '') }}"
                                placeholder="https://facebook.com/username">
                        </div>

                        {{-- Instagram --}}
                        <div class="col-md-6 mb-3">
                            <label for="instagram">Instagram</label>
                            <input type="url" name="instagram" id="instagram" class="form-control"
                                value="{{ old('instagram', $social->instagram ?? '') }}"
                                placeholder="https://instagram.com/username">
                        </div>

                        {{-- Twitter --}}
                        <div class="col-md-6 mb-3">
                            <label for="twitter">Twitter</label>
                            <input type="url" name="twitter" id="twitter" class="form-control"
                                value="{{ old('twitter', $social->twitter ?? '') }}"
                                placeholder="https://twitter.com/username">
                        </div>

                        {{-- TikTok --}}
                        <div class="col-md-6 mb-3">
                            <label for="tiktok">TikTok</label>
                            <input type="url" name="tiktok" id="tiktok" class="form-control"
                                value="{{ old('tiktok', $social->tiktok ?? '') }}"
                                placeholder="https://tiktok.com/@username">
                        </div>

                        {{-- Telegram --}}
                        <div class="col-md-6 mb-3">
                            <label for="telegram">Telegram</label>
                            <input type="url" name="telegram" id="telegram" class="form-control"
                                value="{{ old('telegram', $social->telegram ?? '') }}" placeholder="https://t.me/username">
                        </div>

                        {{-- Google Maps --}}
                        <div class="col-md-6 mb-3">
                            <label for="google_maps">Google Maps</label>
                            <input type="url" name="google_maps" id="google_maps" class="form-control"
                                value="{{ old('google_maps', $social->google_maps ?? '') }}"
                                placeholder="https://goo.gl/maps/xxxx">
                        </div>

                        {{-- Youtube --}}
                        <div class="col-md-12 mb-3">
                            <label for="youtube">YouTube</label>
                            <input type="url" name="youtube" id="youtube" class="form-control"
                                value="{{ old('youtube', $social->youtube ?? '') }}"
                                placeholder="https://youtube.com/channel/xxxx">
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('pengaturan_web.link_social.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($social) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
