@extends('template.app')
@section('title', 'Ubah Pengguna')
@section('content')
    <div class="page-heading">
        <h3>Ubah Pengguna</h3>
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
        <div class="card">
            <div class="card-body">
                <form action="{{ route('user.update', $data->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ $data->name }}"
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
                                value="{{ $data->username }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tambahkan field untuk ubah password -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Password Baru</label>
                        <div class="col-sm-10">
                            <div class="input-group" id="show_hide_password">
                                <input class="form-control @error('password') is-invalid @enderror" type="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password" name="password"
                                    id="password">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)"><i class="bx bx-hide"></i></a>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Minimal 8 karakter, mengandung huruf besar, kecil, angka, dan
                                simbol</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Konfirmasi Password</label>
                        <div class="col-sm-10">
                            <div class="input-group" id="show_hide_confirmation_password">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                    type="password" placeholder="Konfirmasi password baru" name="password_confirmation"
                                    id="password_confirmation">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)"><i class="bx bx-hide"></i></a>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('status_aktif') is-invalid @enderror select_option"
                                name="status_aktif" id="status_aktif" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                <option value="0" {{ $data->status_aktif == 0 ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="1" {{ $data->status_aktif == 1 ? 'selected' : '' }}>
                                    Aktif</option>
                            </select>
                            @error('status_aktif')
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
                                    <option value="{{ $peran->id }}" data-role-name="{{ $peran->name }}"
                                        {{ $data->role == $peran->id ? 'selected' : '' }}>
                                        {{ $peran->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3" id="supplier_selection" style="display: none;">
                        <label class="col-sm-2 col-form-label">Supplier</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('supplier_id') is-invalid @enderror select_option"
                                name="supplier_id" id="supplier_id" data-placeholder="Silahkan Pilih Supplier">
                                <option value="" selected>Silahkan Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ isset($data->id_supplier) && $data->id_supplier == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jabatan</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('jabatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="jabatan" id="jabatan" value="{{ $data->jabatan }}">
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

            function toggleSupplierSelection() {
                var selectedOption = $('#role option:selected');
                var roleName = selectedOption.data('role-name');

                if (roleName && roleName.toLowerCase() === 'supplier') {
                    $('#supplier_selection').show();
                } else {
                    $('#supplier_selection').hide();
                    $('#supplier_id').val(''); // Reset supplier selection
                }
            }

            // Event listener for role change
            $('#role').change(function() {
                toggleSupplierSelection();
            });

            // Check on page load for existing data
            toggleSupplierSelection();
        });
    </script>
@endpush
