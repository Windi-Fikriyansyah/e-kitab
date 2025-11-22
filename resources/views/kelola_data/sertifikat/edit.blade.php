@extends('template.app')
@section('title', 'Edit Sertifikat')
@section('content')
    <div class="page-heading">
        <h3>Edit Sertifikat</h3>
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
                <button type="button" class="btn btn-success" onclick="handleViewFiles('{{ $dataSertifikat->id }}', '{{ $dataSertifikat->nomorRegister }}')">
                    <i class="fas fa-file-alt me-1"></i>Lihat File
                </button>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('kelola_data.sertifikat.update', $dataSertifikat->id) }}">

                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">SKPD</label>
                        <div class="col-sm-10">
                            <select class="form-select @error('kodeSkpd') is-invalid @enderror select_option"
                                name="kodeSkpd" data-placeholder="Silahkan Pilih">
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach ($daftarSkpd as $skpd)
                                    <option value="{{ $skpd->kodeSkpd }}"
                                        {{ $dataSertifikat->kodeSkpd == $skpd->kodeSkpd ? 'selected' : '' }}>
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
                                value="{{ old('nomorRegister', $dataSertifikat->nomorRegister) }}" readonly style="background-color: #e9ecef">
                            @error('nomorRegister')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Nib</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nib') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nib" name="nib" value="{{ old('nib', $dataSertifikat->nib) }}">
                            @error('nib')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Sertifkat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('nomorSertifikat') is-invalid @enderror" type="text"
                                placeholder="Isi dengan nomor sertifikat" name="nomorSertifikat" value="{{ old('nomorSertifikat', $dataSertifikat->nomorSertifikat) }}">
                            @error('nomorSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Tanggal Sertifikat</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('tanggalSertifikat') is-invalid @enderror" type="date"
                                placeholder="Isi dengan tanggal sertifikat" name="tanggalSertifikat" value="{{ old('tanggalSertifikat', $dataSertifikat->tanggalSertifikat) }}">
                            @error('tanggalSertifikat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Luas</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('luas') is-invalid @enderror" type="text"
                                placeholder="Isi dengan luas" name="luas" value="{{ old('luas', $dataSertifikat->luas) }}">
                            @error('luas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <label class="col-sm-2 col-form-label">Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('hak') is-invalid @enderror" type="text"
                                placeholder="Isi dengan hak" name="hak" value="{{ old('hak', $dataSertifikat->hak) }}">
                            @error('hak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pemegang Hak</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('pemegangHak') is-invalid @enderror" type="text"
                                placeholder="Isi dengan pemegang hak" name="pemegangHak" value="{{ old('pemegangHak', $dataSertifikat->pemegangHak) }}">
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
                                        {{ $dataSertifikat->asalUsul == $asal->id ? 'selected' : '' }}>
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
                                value="{{ old('alamat', $dataSertifikat->alamat) }}">
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div><label class="col-sm-2 col-form-label">Sertifkat Asli</label>
                        <div class="col-sm-4">
                            <select class="form-control @error('sertifikatAsli') is-invalid @enderror" name="sertifikatAsli">
                                <option value="" disabled {{ old('sertifikatAsli', $dataSertifikat->sertifikatAsli) === null ? 'selected' : '' }}>Pilih Sertifikat Asli</option>
                                <option value="1" {{ old('sertifikatAsli', $dataSertifikat->sertifikatAsli) == '1' ? 'selected' : '' }}>Ya</option>
                                <option value="0" {{ old('sertifikatAsli', $dataSertifikat->sertifikatAsli) == '0' ? 'selected' : '' }}>Tidak</option>
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
                                <option value="" disabled {{ old('balikNama', $dataSertifikat->balikNama) === null ? 'selected' : '' }}>Pilih Balik Nama</option>
                                <option value="1" {{ old('balikNama', $dataSertifikat->balikNama) == '1' ? 'selected' : '' }}>Sudah</option>
                                <option value="0" {{ old('balikNama', $dataSertifikat->balikNama) == '0' ? 'selected' : '' }}>Belum</option>
                            </select>

                            @error('balikNama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Penggunaan</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('penggunaan') is-invalid @enderror" type="text"
                                placeholder="Isi dengan penggunaan" name="penggunaan" value="{{ old('penggunaan', $dataSertifikat->penggunaan) }}">
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
                                value="{{ old('keterangan', $dataSertifikat->keterangan) }}">
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="col-sm-2 col-form-label">Nibbar</label>
                        <div class="col-sm-4">
                            <input class="form-control @error('Nibbar') is-invalid @enderror" type="text"
                                placeholder="Isi dengan Nibbar" name="Nibbar" value="{{ old('Nibbar', $dataSertifikat->Nibbar) }}">
                            @error('Nibbar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mb-3 text-end">
                        @if ($dataSertifikat->statusSertifikat == '0' && $dataSertifikat->statusPinjam == '0')
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        @endif
                        <a href="{{ route('kelola_data.sertifikat.index') }}" class="btn btn-warning">Kembali</a>
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
                                <i class="fas fa-file-pdf me-1"></i>File Sertifikat
                            </button>
                        </li>
                    </ul>
                    @if ($dataSertifikat->statusSertifikat == '0' && $dataSertifikat->statusPinjam == '0')
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabSuratPenunjukan">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-success" onclick="handleFileEdit('file')">
                                    <i class="fas fa-edit me-1"></i>Edit File
                                </button>
                            </div>
                            <iframe id="iframeSuratPenunjukan" class="w-100" style="height: 500px;" frameborder="0"></iframe>
                        </div>

                    </div>
                    @endif
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabSuratPenunjukan">
                            <iframe id="iframeSuratPenunjukan" class="w-100" style="height: 500px;" frameborder="0"></iframe>
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
                        <input type="hidden" id="nomorRegister" name="nomorRegister">
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
                        <input type="hidden" id="nomorRegister" name="nomorRegister">
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


        $('#fileEditForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('bpkbId', currentBpkbId);
        formData.append('nomorRegister', currentNomorBpkb);

        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('kelola_data.sertifikat.updatefile') }}",
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
        'file': 'File Sertifikat',

    };

    $('#fileType').val(fileType);
    $('#fileEditLabel').text(fileLabels[fileType]);
    $('#bpkbId').val(currentBpkbId);
    $('#nomorRegister').val(currentNomorBpkb);

    $('#fileViewModal').modal('hide');
    $('#fileEditModal').modal('show');

    // Handle modal closing
    $('#fileEditModal').on('hidden.bs.modal', function () {
        $('#fileViewModal').modal('show');
    });
}

function handleViewFiles(bpkbId, nomorRegister) {
    // Show loading state
    currentBpkbId = bpkbId;
    currentNomorBpkb = nomorRegister;
    Swal.fire({
        title: 'Memuat File...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Fetch file data
    $.ajax({
        url: `{{ route('kelola_data.sertifikat.get-files', ['id' => '__bpkbId__']) }}`.replace('__bpkbId__', bpkbId),
        type: 'GET',
        success: function(response) {
            Swal.close();

            // Ensure response contains the 'file' field and it's a valid path
            if (response && response.file) {
                const baseUrl = '{{ asset("/storage/uploads/sertifikat") }}';
                const filePath = `${baseUrl}/${response.file}`;

                // Check if the file path exists
                $.ajax({
                    url: filePath,
                    type: 'HEAD',  // Check if the file exists with a HEAD request
                    success: function() {
                        // Set iframe sources if file exists
                        document.getElementById('iframeSuratPenunjukan').src = filePath;

                        // Show modal after iframe source is set
                        new bootstrap.Modal(document.getElementById('fileViewModal')).show();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Tidak Ditemukan',
                            text: 'File yang diminta tidak ditemukan. Silakan periksa file tersebut.',
                            confirmButtonText: 'Tutup'
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Data File Tidak Valid',
                    text: 'Data file tidak ditemukan atau tidak valid.',
                    confirmButtonText: 'Tutup'
                });
            }
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

        function handleFileEdit(fileType) {
    const fileLabels = {
        'file': 'File Sertifikat',

    };

    $('#fileType').val(fileType);
    $('#fileEditLabel').text(fileLabels[fileType]);
    $('#bpkbId').val(currentBpkbId);
    $('#nomorRegister').val(currentNomorBpkb);

    $('#fileViewModal').modal('hide');
    $('#fileEditModal').modal('show');

    // Handle modal closing
    $('#fileEditModal').on('hidden.bs.modal', function () {
        $('#fileViewModal').modal('show');
    });
}

        function handleViewFiles(bpkbId, nomorRegister) {
        // Show loading state
        currentBpkbId = bpkbId;
        currentNomorBpkb = nomorRegister;
        Swal.fire({
            title: 'Memuat File...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch file data
        $.ajax({
            url: `/kelola_data/sertifikat/get-files/${bpkbId}`,
            type: 'GET',
            success: function(response) {
                Swal.close();

                // Set iframe sources
                const baseUrl = '{{ asset("storage/uploads/sertifikat") }}';
                document.getElementById('iframeSuratPenunjukan').src =
                    `${baseUrl}/${response.file}`;


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
