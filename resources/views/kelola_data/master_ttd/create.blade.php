@extends('template.app')
@section('title', 'Tambah Master TTD')
@section('content')
    <div class="page-heading">
        <h3>Tambah Master TTD</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form method="POST"action="{{ route('kelola_data.master_ttd.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">NIP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi NIP" name="nip">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi nama" name="nama">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">Jabatan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi jabatan" name="jabatan">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 form-label">Pangkat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('pangkat') is-invalid @enderror" autofocus
                                placeholder="Silahkan isi pangkat" name="pangkat">
                            @error('pangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="kodeSkpd" class="col-sm-2 form-label">Kode Skpd</label>
                        <div class="col-sm-10">
                            <select class="choices form-select  @error('kodeSkpd') is-invalid @enderror" name="kodeSkpd" id="kodeSkpd" >
                                <option value="">Pilih Kd Skpd</option>
                                @foreach($masterskpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}">{{ $skpd->kodeSkpd }}</option>
                                @endforeach
                            </select>
                            @error('kodeSkpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('kelola_data.master_ttd.index') }}" class="btn btn-warning">Kembali</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

