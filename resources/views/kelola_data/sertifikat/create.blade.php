@extends('template.app')
@section('title', 'Tambah Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Tambah Sertifikat</h3>
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
                <form method="POST" action="{{ route('kelola_data.sertifikat.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('kodeSkpd') is-invalid @enderror select_option"
                                name="kodeSkpd" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarSkpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}"
                                        {{ old('kodeSkpd') == $skpd->kodeSkpd ? 'selected' : '' }}>
                                        {{ $skpd->namaSkpd }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kodeSkpd')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Arsip Dokumen</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorRegister') is-invalid @enderror" type="text"
                                placeholder="Tidak perlu diisi, otomatis" name="nomorRegister"
                                value="{{ old('nomorRegister', $formattedNumber) }}" readonly style="background-color: #e9ecef">
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nib</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nib') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nib" name="nib" value="{{ old('nib') }}">
                            @error('nib')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Sertifkat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorSertifikat') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor sertifikat" name="nomorSertifikat" value="{{ old('nomorSertifikat') }}">
                            @error('nomorSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tanggal Sertifikat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggalSertifikat') is-invalid @enderror" type="date"
                                placeholder="Isi dengan tanggal sertifikat" name="tanggalSertifikat" value="{{ old('tanggalSertifikat') }}">
                            @error('tanggalSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Luas</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('luas') is-invalid @enderror" type="text"
                                placeholder="Isi dengan luas" name="luas" value="{{ old('luas') }}">
                            @error('luas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('hak') is-invalid @enderror" type="text"
                                placeholder="Isi dengan hak" name="hak" value="{{ old('hak') }}">
                            @error('hak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pemegang Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('pemegangHak') is-invalid @enderror" type="text"
                                placeholder="Isi dengan pemegang hak" name="pemegangHak" value="{{ old('pemegangHak') }}">
                            @error('pemegangHak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Asal-Usul</label>
                        <div class="col-sm-4">
                            <select class="form-select @error('asalUsul') is-invalid @enderror select_option"
                            name="asalUsul" data-placeholder="Silahkan Pilih">
                            <option value="" selected>Silahkan Pilih</option>
                            @foreach ($daftarasalUsul as $asal)
                                <option value="{{ $asal->id }}"
                                    {{ old('id') == $asal->id ? 'selected' : '' }}>
                                    {{ $asal->nama }}
                                </option>
                            @endforeach
                        </select>
                            @error('asalUsul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('alamat') is-invalid @enderror" type="text"
                                placeholder="Isi dengan alamat" name="alamat"
                                value="{{ old('alamat') }}">
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Sertifkat Asli</label>
                        <div class="col-sm-4">
                            <select class="form-control @error('sertifikatAsli') is-invalid @enderror" name="sertifikatAsli">
                                <option value="" disabled selected>Pilih Sertifkat Asli</option>
                                <option value="1" {{ old('sertifikatAsli') == 'Ya' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('sertifikatAsli') == 'Tidak' ? 'selected' : '' }}>Tidak</option>

                            </select>
                            @error('sertifikatAsli')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Balik Nama</label>
                        <div class="col-sm-4">
                            <select class="form-control @error('balikNama') is-invalid @enderror" name="balikNama">
                                <option value="" disabled selected>Pilih Balik Nama</option>
                                <option value="1" {{ old('balikNama') == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                                <option value="0" {{ old('balikNama') == 'Belum' ? 'selected' : '' }}>Belum</option>

                            </select>
                            @error('balikNama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Penggunaan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('penggunaan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan penggunaan" name="penggunaan" value="{{ old('penggunaan') }}">
                            @error('penggunaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('keterangan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor rangka" name="keterangan"
                                value="{{ old('keterangan') }}">
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Nibbar</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('Nibbar') is-invalid @enderror" type="text"
                                placeholder="Isi dengan Nibbar" name="Nibbar" value="{{ old('Nibbar') }}">
                            @error('Nibbar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">File</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('file') is-invalid @enderror" type="file" name="file"
                                value="{{ old('file') }}">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                    </div>

                    <div class="mb-3 text-end">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <a href="{{ route('kelola_data.sertifikat.index') }}" class="btn btn-warning">Kembali</a>
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
        });
    </script>
@endpush
