@extends('template.app')
@section('title', 'Ubah Akses')
@section('content')
    <div class="page-heading">
        <h3>Ubah Akses</h3>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-lg-12">
                @if (session('message'))
                    <div class="alert alert-danger">
                        {{ session('message') }}
                    </div>
                @endif
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

                        <!-- General Form Elements -->
                        <form action="{{ route('akses.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label for="name" class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" autofocus
                                        placeholder="Silahkan isi nama" name="name" value="{{ $data->name }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Tipe</label>
                                <div class="col-sm-10">
                                    <select class="form-select select_option @error('tipe') is-invalid @enderror"
                                        name="tipe" id="tipe">
                                        <option value="1" {{ $data->tipe == '1' ? 'selected' : '' }}>Tanpa link
                                        </option>
                                        <option value="2" {{ $data->tipe == '2' ? 'selected' : '' }}>Ada link</option>
                                    </select>
                                    @error('tipe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3" id="input_link" hidden>
                                <label for="inputPassword" class="col-sm-2 col-form-label">Link</label>
                                <div class="col-sm-10">
                                    <input class="form-control @error('link') is-invalid @enderror" type="text"
                                        placeholder="Isi dengan link" name="link" id="link"
                                        value="{{ $data->link }}">
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Parent</label>
                                <div class="col-sm-10">
                                    <select class="form-select select_option @error('parent') is-invalid @enderror"
                                        name="parent" id="parent">
                                        <option value="-" {{ $data->parent == '-' ? 'selected' : '' }}>Tidak
                                            ada
                                        </option>
                                        @foreach ($permissions as $permission)
                                            <option value="{{ $permission->id }}"
                                                {{ $permission->id == $data->parent ? 'selected' : '' }}>
                                                {{ $permission->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12 text-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('akses.index') }}" class="btn btn-warning">Kembali</a>
                                </div>
                            </div>

                        </form><!-- End General Form Elements -->

                    </div>
                </div>

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

            let tipe = "{{ $data->tipe }}";

            cekTipe(tipe)

            $('#tipe').on('change', function() {
                let tipe = $('#tipe').val();

                cekTipe(tipe)
            });

            function cekTipe(tipe) {
                if (tipe == '1') {
                    $('#input_link').prop('hidden', true);
                } else if (tipe == '2') {
                    $('#input_link').prop('hidden', false);
                }
            }
        });
    </script>
@endpush
