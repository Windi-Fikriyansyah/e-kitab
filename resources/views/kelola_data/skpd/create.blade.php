@extends('template.app')
@section('title', 'Tambah Master TTD')
@section('content')
    <div class="page-heading">
        <h3>Tambah Master SKPD</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form method="POST"action="{{ route('kelola_data.skpd.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">Kode SKPD</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('kodeSKPD') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi Kode Skpd" name="kodeSKPD">
                            @error('kodeSKPD')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">Nama SKPD</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('namaSKPD') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi Nama SKPD" name="namaSKPD">
                            @error('namaSKPD')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('kelola_data.skpd.index') }}" class="btn btn-warning">Kembali</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

