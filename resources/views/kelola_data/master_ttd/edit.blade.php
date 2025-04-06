@extends('template.app')
@section('title', 'Edit Master TTD')
@section('content')
    <div class="page-heading">
        <h3>Edit Master TTD</h3>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('kelola_data.master_ttd.update', Crypt::encrypt($MasterTtd->id)) }}">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label for="nomorRegister" class="col-sm-2 form-label">NIP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                   id="nip" name="nip"
                                   placeholder="Silahkan isi nomor register"
                                   value="{{ old('nip', $MasterTtd->nip) }}">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="nib" class="col-sm-2 form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                   id="nama" name="nama"
                                   placeholder="Silahkan isi nama"
                                   value="{{ old('nama', $MasterTtd->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="nomorSertifikat" class="col-sm-2 form-label">Jabatan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                   id="jabatan" name="jabatan"
                                   placeholder="Silahkan isi Jabatan"
                                   value="{{ old('jabatan', $MasterTtd->jabatan) }}">
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="tanggal" class="col-sm-2 form-label">Pangkat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('pangkat') is-invalid @enderror"
                                   id="pangkat" name="pangkat"
                                   value="{{ old('pangkat', $MasterTtd->pangkat) }}">
                            @error('pangkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="kodeSkpd" class="col-sm-2 form-label">Kd Skpd</label>
                        <div class="col-sm-10">
                            <select class="choices form-select  @error('kodeSkpd') is-invalid @enderror" name="kodeSkpd" id="kodeSkpd" >
                                @foreach($masterskpd as $skpd)
                                <option value="{{ $skpd->kodeSkpd }}" {{ old('kodeSkpd', $MasterTtd->kodeSkpd) == $skpd->kodeSkpd ? 'selected' : '' }}>
                                    {{ $skpd->kodeSkpd }}
                                </option>
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
