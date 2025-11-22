@extends('template.app')
@section('title', 'Ubah Peran')
@section('content')
    <div class="page-heading">
        <h3>Ubah Peran</h3>
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
                <form method="POST"action="{{ route('peran.update', $data->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama" name="name" id="name" value="{{ $data->name }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akses</label>
                    </div>
                    <div class="row mb-3">
                        @foreach ($akses_tipe1 as $tipe1)
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 text-uppercase">{{ $tipe1->name }}</h6>
                                                <span><input type="checkbox" class="check" name="check1[]"
                                                        value="{{ $tipe1->id }}"
                                                        @if (is_array($permission) && in_array($tipe1->id, $permission)) checked @endif></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @foreach ($akses_tipe2 as $tipe2)
                                                @if ($tipe1->id == $tipe2->parent)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $tipe2->name }} <span><input type="checkbox" name="akses[]"
                                                                class="checkTipe2" value="{{ $tipe2->id }}"
                                                                data-parent="{{ $tipe2->parent }}" id="akses[]"
                                                                @if (is_array($permission) && in_array($tipe2->id, $permission)) checked @endif></span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @error('akses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('peran.index') }}" class="btn btn-warning">Kembali</a>
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

            // $('.check').on('click', function() {
            //     let checkTipe = this.value;
            //     let check = this.checked;

            //     const allboxes = Array.from(document.querySelectorAll('.checkTipe2'));

            //     const lala = allboxes.map((item) => {
            //         if (item.dataset.parent == checkTipe && check) {
            //             item.checked = true
            //         } else if (!check && item.dataset.parent == checkTipe) {
            //             item.checked = false
            //         }
            //     })
            // });

        });
    </script>
@endpush
