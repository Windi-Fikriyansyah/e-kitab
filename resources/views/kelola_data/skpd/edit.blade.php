@extends('template.app')
@section('title', 'Edit Master TTD')
@section('content')
    <div class="page-heading">
        <h3>Edit Master SKPD</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('kelola_data.skpd.update', Crypt::encrypt($skpd->kodeSkpd)) }}">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label for="nomorRegister" class="col-sm-2 form-label">Kode Skpd</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('kodeSkpd') is-invalid @enderror"
                                   id="kodeSkpd" name="kodeSkpd"
                                   placeholder="Silahkan isi Kode Skpd"
                                   value="{{ old('kodeSkpd', $skpd->kodeSkpd) }}">
                            @error('kodeSkpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="nib" class="col-sm-2 form-label">Nama Skpd</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('namaSkpd') is-invalid @enderror"
                                   id="namaSkpd" name="namaSkpd"
                                   placeholder="Silahkan isi namaSkpd"
                                   value="{{ old('namaSkpd', $skpd->namaSkpd) }}">
                            @error('namaSkpd')
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
