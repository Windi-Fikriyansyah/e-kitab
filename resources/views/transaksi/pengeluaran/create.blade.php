@extends('template.app')
@section('title', isset($pengeluaran) ? 'Edit Pengeluaran' : 'Tambah Pengeluaran')

@section('content')
    <div class="page-heading">
        <h3>{{ isset($pengeluaran) ? 'Edit Pengeluaran' : 'Tambah Pengeluaran' }}</h3>
    </div>

    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($pengeluaran) ? route('transaksi.pengeluaran.update', Crypt::encrypt($pengeluaran->id)) : route('transaksi.pengeluaran.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($pengeluaran))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', $pengeluaran->tanggal ?? date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" id="kategori"
                                class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach (['Operasional', 'Pegawai', 'Sewa', 'Lain-lain'] as $opt)
                                    <option value="{{ $opt }}"
                                        {{ old('kategori', $pengeluaran->kategori ?? '') == $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="metode_bayar" class="form-label">Metode Bayar <span
                                    class="text-danger">*</span></label>
                            <select name="metode_bayar" id="metode_bayar"
                                class="form-select @error('metode_bayar') is-invalid @enderror" required>
                                <option value="">-- Pilih Metode Bayar --</option>
                                @foreach (['Tunai', 'Transfer', 'Hutang Supplier'] as $opt)
                                    <option value="{{ $opt }}"
                                        {{ old('metode_bayar', $pengeluaran->metode_bayar ?? '') == $opt ? 'selected' : '' }}>
                                        {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                            @error('metode_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Contoh: Beli bubble wrap 3 roll">{{ old('deskripsi', $pengeluaran->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nominal" class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="text" id="nominal_display" class="form-control" placeholder="Masukkan nominal"
                                value="{{ isset($pengeluaran) ? 'Rp ' . number_format($pengeluaran->nominal, 0, ',', '.') : '' }}">
                            <input type="hidden" name="nominal" id="nominal_hidden"
                                value="{{ old('nominal', $pengeluaran->nominal ?? '') }}">
                            @error('nominal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-6 mb-3">
                            <label for="lampiran" class="form-label">Lampiran Bukti (Opsional)</label>
                            <input type="file" name="lampiran" id="lampiran"
                                class="form-control @error('lampiran') is-invalid @enderror" accept="image/*">
                            @if (isset($pengeluaran) && $pengeluaran->lampiran)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $pengeluaran->lampiran) }}" alt="Lampiran"
                                        width="120" class="rounded border">
                                    <small class="d-block text-muted mt-1">Lampiran saat ini</small>
                                </div>
                            @endif
                            @error('lampiran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> {{ isset($pengeluaran) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('transaksi.pengeluaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputDisplay = document.getElementById('nominal_display');
            const inputHidden = document.getElementById('nominal_hidden');

            // Fungsi format ke Rupiah
            function formatRupiah(angka) {
                const numberString = angka.replace(/[^,\d]/g, '');
                const split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    const separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah ? 'Rp ' + rupiah : '';
            }

            inputDisplay.addEventListener('input', function(e) {
                const clean = this.value.replace(/[^0-9]/g, '');
                this.value = formatRupiah(clean);
                inputHidden.value = clean; // isi field hidden tanpa format
            });

            // Ketika halaman di-load (untuk edit)
            if (inputHidden.value) {
                inputDisplay.value = formatRupiah(inputHidden.value);
            }
        });
    </script>
@endpush
