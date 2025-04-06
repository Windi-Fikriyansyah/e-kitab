@extends('template.app')
@section('title', 'Tambah Pengguna')
@section('content')
    <div class="page-heading">
        <h3>Tambah Pengguna</h3>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">Error</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form method="POST"action="{{ route('user.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ old('name') }}"
                                autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('username') is-invalid @enderror" type="text"
                                placeholder="Isi dengan username" name="username" id="username"
                                value="{{ old('username') }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <div class="input-group" id="show_hide_password">
                                <input type="password" class="form-control border-end-0" id="password"
                                    placeholder="Silahkan isi password" name="password"> <a href="javascript:;"
                                    class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-10">
                            <div class="input-group" id="show_hide_confirmation_password">
                                <input type="password" class="form-control border-end-0" id="confirmation_password"
                                    placeholder="Silahkan isi kembali password" name="confirmation_password"> <a
                                    href="javascript:;" class="input-group-text bg-transparent"><i
                                        class='bx bx-hide'></i></a>
                            </div>
                            @error('confirmation_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('status_aktif') is-invalid @enderror select_option"
                                name="status_aktif" id="status_aktif" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="1" {{ old('status_aktif') == '1' ? 'selected' : '' }}>
                                    Aktif</option>
                            </select>
                            @error('status_aktif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('tipe') is-invalid @enderror select_option" name="tipe"
                                id="tipe" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="owner" {{ old('tipe') == 'owner' ? 'selected' : '' }}>
                                    Owner</option>
                                <option value="kasir" {{ old('tipe') == 'kasir' ? 'selected' : '' }}>
                                    Kasir</option>
                            </select>
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Peran</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('role') is-invalid @enderror select_option" name="role"
                                id="role" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftar_peran as $peran)
                                    <option value="{{ $peran->id }}"
                                        {{ old('role') == $peran->id ? 'selected' : '' }}>
                                        {{ $peran->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jabatan</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('jabatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="jabatan" id="jabatan"
                                value="{{ old('jabatan') }}">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('user.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });

            $("#show_hide_confirmation_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_confirmation_password input').attr("type") == "text") {
                    $('#show_hide_confirmation_password input').attr('type', 'password');
                    $('#show_hide_confirmation_password i').addClass("bx-hide");
                    $('#show_hide_confirmation_password i').removeClass("bx-show");
                } else if ($('#show_hide_confirmation_password input').attr("type") == "password") {
                    $('#show_hide_confirmation_password input').attr('type', 'text');
                    $('#show_hide_confirmation_password i').removeClass("bx-hide");
                    $('#show_hide_confirmation_password i').addClass("bx-show");
                }
            });
        });
    </script>
@endpush
