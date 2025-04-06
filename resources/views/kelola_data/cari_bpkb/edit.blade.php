@extends('template.app')
@section('title', 'Edit BPKB')
@section('content')
    <div class="page-heading">
        <h3>Edit BPKB</h3>
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

            <div class="card-header d-flex justify-content-end">
                <button type="button" class="btn btn-success" onclick="handleViewFiles('{{ $dataBpkb->id }}', '{{ $dataBpkb->nomorBpkb }}')">
                    <i class="fas fa-file-alt me-1"></i>Lihat File
                </button>
            </div>
            <div class="card-body">
                <form method="POST"action="{{ route('kelola_data.caribpkb.update', $dataBpkb->id) }}">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-10">
                            <select disabled class="form-select @error('kodeSkpd') is-invalid @enderror select_option"
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
                            <input disabled class="form-control @error('nomorRegister') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor register" name="nomorRegister"
                                value="{{ $dataBpkb->nomorRegister }}" autofocus readonly style="background-color: #e9ecef">
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Nomor BPKB</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorBpkb') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor bpkb" name="nomorBpkb" value="{{ $dataBpkb->nomorBpkb }}" disabled
                                readonly>
                            @error('nomorBpkb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Polisi</label>
                        <div class="col-sm-4">
                            <input disabled class="form-control @error('nomorPolisi') is-invalid @enderror" type="text"
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
                                value="{{ $dataBpkb->tahunPembuatan }}" disabled>
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

                        <a href="{{ route('kelola_data.caribpkb.index') }}" class="btn btn-warning">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fileViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lihat File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="refreshPage()"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs nav-justified mb-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabSuratPenunjukan">
                                <i class="fas fa-file-pdf me-1"></i>Surat Penunjukan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabBA">
                                <i class="fas fa-file-pdf me-1"></i>File BA
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#filepaktaintegritas">
                                <i class="fas fa-file-pdf me-1"></i>File Pakta Integritas
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#filebpkb">
                                <i class="fas fa-file-pdf me-1"></i>File BPKB
                            </button>
                        </li>
                    </ul>
                    @if ($dataBpkb->statusBpkb == '0' && $dataBpkb->statusPinjam == '0')
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabSuratPenunjukan">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-success" onclick="handleFileEdit('suratPenunjukan')">
                                    <i class="fas fa-edit me-1"></i>Edit File
                                </button>
                            </div>
                            <iframe id="iframeSuratPenunjukan" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="tabBA">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-success" onclick="handleFileEdit('ba')">
                                    <i class="fas fa-edit me-1"></i>Edit File
                                </button>
                            </div>
                            <iframe id="iframeBA" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="filepaktaintegritas">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-success" onclick="handleFileEdit('paktaIntegritas')">
                                    <i class="fas fa-edit me-1"></i>Edit File
                                </button>
                            </div>
                            <iframe id="iframepaktaintegritas" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="filebpkb">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-success" onclick="handleFileEdit('bpkb')">
                                    <i class="fas fa-edit me-1"></i>Edit File
                                </button>
                            </div>
                            <iframe id="iframebpkb" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                    </div>
                    @endif
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabSuratPenunjukan">
                            <iframe id="iframeSuratPenunjukan" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="tabBA">
                            <iframe id="iframeBA" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="filepaktaintegritas">
                            <iframe id="iframepaktaintegritas" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                        <div class="tab-pane fade" id="filebpkb">
                            <iframe id="iframebpkb" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fileEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="fileEditForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="fileType" name="fileType">
                        <input type="hidden" id="bpkbId" name="bpkbId">
                        <input type="hidden" id="nomorBpkb" name="nomorBpkb">
                        <div class="mb-3">
                            <label class="form-label" id="fileEditLabel"></label>
                            <input type="file" class="form-control" name="file" accept="application/pdf" required>
                            <div class="form-text">Format file: PDF, Maksimal 2MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
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
            url: "{{ route('kelola_data.caribpkb.update-file') }}",
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
            url: `{{ route('kelola_data.caribpkb.get-files', ['id' => '__bpkbId__']) }}`.replace('__bpkbId__', bpkbId),
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
