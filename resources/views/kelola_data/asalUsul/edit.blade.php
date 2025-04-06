@extends('template.app')
@section('title', 'Edit Master AsalUsul')
@section('content')
    <div class="page-heading">
        <h3>Edit Master AsalUsul</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('kelola_data.asalUsul.update', Crypt::encrypt($asalUsul->id)) }}">
                    @csrf
                    @method('PUT')


                    <div class="row mb-3">
                        <label for="nib" class="col-sm-2 form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                   id="nama" name="nama"
                                   placeholder="Silahkan isi nama"
                                   value="{{ old('nama', $asalUsul->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('kelola_data.asalUsul.index') }}" class="btn btn-warning">Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
