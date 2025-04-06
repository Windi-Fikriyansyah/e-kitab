@extends('template.app')
@section('title', 'Edit BPKB')
@section('content')
    <div class="page-heading">
        <h3>History BPKB</h3>
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
                <form method="POST"action="">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('kodeSkpd') is-invalid @enderror select_option"
                                name="kodeSkpd" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarSkpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}"
                                        {{ $dataBpkb->kodeSkpd == $skpd->kodeSkpd ? 'selected' : '' }}>
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
                                placeholder="Isi dengan nomor register" name="nomorRegister"
                                value="{{ $dataBpkb->nomorRegister }}" autofocus readonly style="background-color: #e9ecef">
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nomor BPKB</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkb') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor bpkb" name="nomorBpkb" value="{{ $dataBpkb->nomorBpkb }}">
                            @error('nomorBpkb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorPolisi') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor polisi" name="nomorPolisi"
                                value="{{ $dataBpkb->nomorPolisi }}">
                            @error('nomorPolisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namaPemilik') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nama pemilik" name="namaPemilik"
                                value="{{ $dataBpkb->namaPemilik }}">
                            @error('namaPemilik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('jenis') is-invalid @enderror" type="text"
                                placeholder="Isi dengan jenis" name="jenis" value="{{ $dataBpkb->jenis }}">
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Merk</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('merk') is-invalid @enderror" type="text"
                                placeholder="Isi dengan merk" name="merk" value="{{ $dataBpkb->merk }}">
                            @error('merk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tipe</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tipe') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tipe" name="tipe" value="{{ $dataBpkb->tipe }}">
                            @error('tipe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Model</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('model') is-invalid @enderror" type="text"
                                placeholder="Isi dengan model" name="model" value="{{ $dataBpkb->model }}">
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Tahun Pembuatan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPembuatan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tahun pembuatan" name="tahunPembuatan"
                                value="{{ $dataBpkb->tahunPembuatan }}">
                            @error('tahunPembuatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tahun Perakitan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tahunPerakitan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan tahun perakitan" name="tahunPerakitan"
                                value="{{ $dataBpkb->tahunPerakitan }}">
                            @error('tahunPerakitan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Isi Silinder</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('isiSilinder') is-invalid @enderror" type="text"
                                placeholder="Isi dengan isi silinder" name="isiSilinder"
                                value="{{ $dataBpkb->isiSilinder }}">
                            @error('isiSilinder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Warna</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('warna') is-invalid @enderror" type="text"
                                placeholder="Isi dengan warna" name="warna" value="{{ $dataBpkb->warna }}">
                            @error('warna')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Rangka</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorRangka') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor rangka" name="nomorRangka"
                                value="{{ $dataBpkb->nomorRangka }}">
                            @error('nomorRangka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Nomor Mesin</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorMesin') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor mesin" name="nomorMesin"
                                value="{{ $dataBpkb->nomorMesin }}">
                            @error('nomorMesin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorPolisiLama') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor polisi lama" name="nomorPolisiLama"
                                value="{{ $dataBpkb->nomorPolisiLama }}">
                            @error('nomorPolisiLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nomor Bpkb Lama</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkbLama') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor bpkb lama" name="nomorBpkbLama"
                                value="{{ $dataBpkb->nomorBpkbLama }}">
                            @error('nomorBpkbLama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nibbar</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('Nibbar') is-invalid @enderror" type="text"
                                placeholder="Isi dengan Nibbar" name="Nibbar"
                                value="{{ $dataBpkb->Nibbar }}">
                            @error('Nibbar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nama Penerima Kendaraan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('namapenerimakendaraan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan Nama Penerima Kendaraan" name="namapenerimakendaraan"
                                value="{{ $dataBpkb->namapenerimakendaraan }}">
                            @error('namapenerimakendaraan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('alamat') is-invalid @enderror" type="text" placeholder="Isi dengan alamat"
                                name="alamat">{{ $dataBpkb->alamat }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Keterangan</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan keterangan" name="keterangan">{{ $dataBpkb->keterangan }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 text-end">

                        <a href="{{ route('kelola_data.historybpkb.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('js')
<script>
    function refreshPage() {
        // Menutup modal terlebih dahulu
        $('#fileViewModal').modal('hide');

        // Me-refresh halaman setelah modal ditutup
        location.reload();
    }
</script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

         // Handle file edit form submission
    $('#fileEditForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('bpkbId', currentBpkbId);
        formData.append('nomorBpkb', currentNomorBpkb);

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('kelola_data.historybpkb.update-file') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'File berhasil diperbarui',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#fileEditModal').modal('hide');
                    handleViewFiles(currentBpkbId, currentNomorBpkb);
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'Tutup'
                });
            }
        });
    });

        function handleFileEdit(fileType) {
    const fileLabels = {
        'suratPenunjukan': 'File Surat Penunjukan',
        'ba': 'File Berita Acara',
        'paktaIntegritas': 'File Pakta Integritas',
        'bpkb': 'file bpkb',
    };

    $('#fileType').val(fileType);
    $('#fileEditLabel').text(fileLabels[fileType]);
    $('#bpkbId').val(currentBpkbId);
    $('#nomorBpkb').val(currentNomorBpkb);

    $('#fileViewModal').modal('hide');
    $('#fileEditModal').modal('show');

    // Handle modal closing
    $('#fileEditModal').on('hidden.bs.modal', function () {
        $('#fileViewModal').modal('show');
    });
}

        function handleViewFiles(bpkbId, nomorBpkb) {
        // Show loading state
        currentBpkbId = bpkbId;
        currentNomorBpkb = nomorBpkb;
        Swal.fire({
            title: 'Memuat File...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch file data
        $.ajax({
            url: `{{ route('kelola_data.historybpkb.get-files', ['id' => '__bpkbId__']) }}`.replace('__bpkbId__', bpkbId),
            type: 'GET',
            success: function(response) {
                Swal.close();

                // Set iframe sources
                const baseUrl = '{{ asset("/storage/uploads/bpkb") }}';
                document.getElementById('iframeSuratPenunjukan').src =
                    `${baseUrl}/file_surat_penunjukan/${response.filesuratpenunjukan}`;
                document.getElementById('iframeBA').src =
                    `${baseUrl}/file_ba/${response.fileba}`;
                document.getElementById('iframepaktaintegritas').src =
                    `${baseUrl}/file_pakta_integritas/${response.filepaktaintegritas}`;
                document.getElementById('iframebpkb').src =
                    `${baseUrl}/file_bpkb/${response.filebpkb}`;

                // Show modal
                new bootstrap.Modal(document.getElementById('fileViewModal')).show();
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat file. Silakan coba lagi.',
                    confirmButtonText: 'Tutup'
                });
                console.error('Error loading files:', xhr);
            }
        });
}
    </script>
@endpush
