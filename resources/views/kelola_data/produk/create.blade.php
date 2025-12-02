@extends('template.app')
@section('title', isset($produk) ? 'Edit Produk' : 'Tambah Produk')
@section('content')
    <div class="page-heading">
        <h2>{{ isset($produk) ? 'Edit Produk' : 'Tambah Produk' }}</h2>
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
                <form id="produkForm"
                    action="{{ isset($produk) ? route('kelola_data.produk.update', Crypt::encrypt($produk->id_produk_asli)) : route('kelola_data.produk.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($produk))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <!-- Kolom Pertama (Arab) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="judul_arab">Judul (Arab)</label>
                                <input type="text" name="judul_arab" id="judul_arab" class="form-control text-right"
                                    dir="rtl" value="{{ old('judul_arab', $produk->judul ?? '') }}"
                                    placeholder="Judul dalam bahasa Arab">
                            </div>

                            <!-- Replace the kategori select section with this: -->
                            <div class="form-group">
                                <label for="kategori">Kategori (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select" name="kategori[]" id="kategori" multiple>

                                        @if (isset($kategori))
                                            @foreach ($kategori as $k)
                                                <option value="{{ $k->nama_arab ?? '' }}"
                                                    data-nama_arab="{{ $k->nama_arab ?? '' }}"
                                                    data-nama_indonesia="{{ $k->nama_indonesia ?? '' }}" selected>
                                                    {{ $k->text ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif


                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('kategori')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="selected_kategori" class="mt-2"></div>



                            <div class="form-group">
                                <label for="sub_kategori">Sub Kategori (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select" name="sub_kategori[]" id="sub_kategori" multiple>

                                        @if (isset($subKategori))
                                            @foreach ($subKategori as $s)
                                                <option value="{{ $s->nama_arab }}"
                                                    data-nama_arab="{{ $s->nama_arab ?? '' }}"
                                                    data-nama_indonesia="{{ $s->nama_indonesia ?? '' }}" selected>
                                                    {{ $s->text ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif


                                    </select>
                                    <button type="button" class="btn btn-success"
                                        onclick="bukaModalTambah('sub_kategori')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('sub_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="selected_sub_kategori" class="mt-2"></div>


                            <div class="form-group">
                                <label for="penerbit">Penerbit (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('penerbit') is-invalid @enderror" name="penerbit"
                                        dir="rtl" id="penerbit" style="width: 100%">
                                        @if (isset($produk) && $produk->penerbit)
                                            <option value="{{ $produk->penerbit }}" selected>
                                                {{ $produk->penerbit }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('penerbit')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('penerbit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kedua (Indonesia) -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="judul_indonesia">Judul (Indonesia)</label>
                                <input type="text" name="judul_indonesia" id="judul_indonesia" class="form-control"
                                    value="{{ old('judul_indonesia', $produk->judul_indo ?? '') }}"
                                    placeholder="Judul dalam bahasa Indonesia">
                            </div>

                            <div class="form-group">
                                <label for="kategori_indonesia">Kategori (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="kategori_indonesia"
                                    id="kategori_indonesia"
                                    value="{{ old('kategori_indonesia', isset($produk) && $produk->kategori ? $produk->kategori_indo : '') }}"
                                    readonly>

                            </div>

                            <div class="form-group">
                                <label for="sub_kategori_indonesia">Sub Kategori (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="sub_kategori_indonesia"
                                    id="sub_kategori_indonesia"
                                    value="{{ old('sub_kategori_indonesia', isset($produk) && $produk->sub_kategori ? $produk->sub_kategori_indo : '') }}"
                                    readonly>

                            </div>

                            <div class="form-group">
                                <label for="penerbit_indonesia">Penerbit (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;"
                                    style="background-color: #e9ecef; color: #6c757d;" name="penerbit_indonesia"
                                    id="penerbit_indonesia"
                                    value="{{ old('penerbit_indonesia', isset($produk) && $produk->penerbit ? $produk->penerbit_indo : '') }}"
                                    required readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Rest of your form remains the same -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cover">Cover (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('cover') is-invalid @enderror" name="cover"
                                        dir="rtl" id="cover" style="width: 100%">
                                        @if (isset($produk) && $produk->cover)
                                            <option value="{{ $produk->cover }}" selected>
                                                {{ $produk->cover }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('cover')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('cover')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="kertas">Kertas (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('kertas') is-invalid @enderror" name="kertas"
                                        dir="rtl" id="kertas" style="width: 100%">
                                        @if (isset($produk) && $produk->kertas)
                                            <option value="{{ $produk->kertas }}" selected>
                                                {{ $produk->kertas }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('kertas')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('kertas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kualitas">Kualitas (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('kualitas') is-invalid @enderror" name="kualitas"
                                        dir="rtl" id="kualitas" style="width: 100%">
                                        @if (isset($produk) && $produk->kualitas)
                                            <option value="{{ $produk->kualitas }}" selected>
                                                {{ $produk->kualitas }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('kualitas')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('kualitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="harakat">Harakat (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('harakat') is-invalid @enderror" name="harakat"
                                        dir="rtl" id="harakat" style="width: 100%">
                                        @if (isset($produk) && $produk->harakat)
                                            <option value="{{ $produk->harakat }}" selected>
                                                {{ $produk->harakat }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('harakat')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('harakat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="penulis">Penulis (Arab)</label>
                                <div class="input-group">
                                    <select class="form-select @error('penulis') is-invalid @enderror" name="penulis"
                                        dir="rtl" id="penulis" style="width: 100%">
                                        @if (isset($produk) && $produk->penulis)
                                            <option value="{{ $produk->penulis }}" selected>
                                                {{ $produk->penulis }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('penulis')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('penulis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cover_indonesia">Cover (Indonesia)</label>
                                <input type="text" class="form-control" name="cover_indonesia"
                                    style="background-color: #e9ecef; color: #6c757d;" id="cover_indonesia"
                                    value="{{ old('cover_indonesia', isset($produk) && $produk->cover ? $produk->cover_indo : '') }}"
                                    required readonly>
                            </div>

                            <div class="form-group">
                                <label for="kertas_indonesia">Kertas (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="kertas_indonesia"
                                    id="kertas_indonesia"
                                    value="{{ old('kertas_indonesia', isset($produk) && $produk->kertas ? $produk->kertas_indo : '') }}"
                                    required readonly>
                            </div>

                            <div class="form-group">
                                <label for="kualitas_indonesia">Kualitas (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="kualitas_indonesia"
                                    id="kualitas_indonesia"
                                    value="{{ old('kualitas_indonesia', isset($produk) && $produk->kualitas ? $produk->kualitas_indo : '') }}"
                                    required readonly>
                            </div>


                            <div class="form-group">
                                <label for="harakat_indonesia">Harakat (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="harakat_indonesia"
                                    id="harakat_indonesia"
                                    value="{{ old('harakat_indonesia', isset($produk) && $produk->harakat ? $produk->harakat_indo : '') }}"
                                    required readonly>
                            </div>

                            <div class="form-group">
                                <label for="penulis_indonesia">Penulis (Indonesia)</label>
                                <input type="text" class="form-control"
                                    style="background-color: #e9ecef; color: #6c757d;" name="penulis_indonesia"
                                    id="penulis_indonesia"
                                    value="{{ old('penulis_indonesia', isset($produk) && $produk->penulis ? $produk->penulis_indo : '') }}"
                                    required readonly>
                            </div>


                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <div class="input-group">
                                    <select class="form-select @error('supplier') is-invalid @enderror" name="supplier"
                                        id="supplier" style="width: 100%">
                                        @if (isset($produk) && $produk->supplier)
                                            <option value="{{ $produk->supplier }}" selected>
                                                {{ $produk->nama_supplier }} | {{ $produk->telepon }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('supplier')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="halaman">Halaman</label>
                                <input type="text" name="halaman" id="halaman" class="form-control"
                                    value="{{ old('halaman', $produk->halaman ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="berat">Berat (gram)</label>
                                <input type="text" name="berat" id="berat" class="form-control"
                                    value="{{ old('berat', $produk->berat ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ukuran">Ukuran</label>
                                <div class="input-group">
                                    <select class="form-select @error('ukuran') is-invalid @enderror" name="ukuran"
                                        id="ukuran" style="width: 100%">
                                        @if (isset($produk) && $produk->ukuran)
                                            <option value="{{ $produk->ukuran }}" selected>
                                                {{ $produk->ukuran }}
                                            </option>
                                        @endif
                                    </select>
                                    <button type="button" class="btn btn-success" onclick="bukaModalTambah('ukuran')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('ukuran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tambahkan setelah bagian harga jual -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="harga_modal_display">Harga Modal</label>
                                <input type="text" id="harga_modal_display" class="form-control"
                                    value="{{ old('harga_modal', isset($produk) ? number_format($produk->harga_modal, 0, ',', '.') : '') }}"
                                    placeholder="0">
                                <input type="hidden" name="harga_modal" id="harga_modal"
                                    value="{{ old('harga_modal', $produk->harga_modal ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="harga_jual_display">Harga Jual</label>
                                <input type="text" id="harga_jual_display" class="form-control"
                                    value="{{ old('harga_jual', isset($produk) ? number_format($produk->harga_jual, 0, ',', '.') : '') }}"
                                    placeholder="0">
                                <input type="hidden" name="harga_jual" id="harga_jual"
                                    value="{{ old('harga_jual', $produk->harga_jual ?? '') }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="laba_display">Laba (Otomatis)</label>
                                <input type="text" id="laba_display" class="form-control" readonly
                                    value="{{ old('laba', isset($produk) ? number_format($produk->harga_jual - $produk->harga_modal, 0, ',', '.') : '0') }}"
                                    placeholder="0" style="background-color: #e9ecef;">
                                <input type="hidden" name="laba" id="laba"
                                    value="{{ old('laba', isset($produk) ? $produk->harga_jual - $produk->harga_modal : '0') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control"
                            value="{{ old('stok', $produk->stok ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <div id="imageUploadContainer">
                            <div class="input-group mb-3">
                                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                                <button type="button" class="btn btn-success add-more-btn"
                                    onclick="addMoreImageField()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        @if (isset($produk) && $produk->images)
                            <div class="mt-3">
                                <h6>Gambar Saat Ini:</h6>
                                <div class="row">
                                    @foreach (json_decode($produk->images) as $image)
                                        <div class="col-md-3 mb-3">
                                            <img src="{{ asset('storage/products/' . $image) }}" class="img-thumbnail"
                                                style="width: 100%; height: 150px; object-fit: cover;">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]"
                                                    value="{{ $image }}" id="delete-{{ $loop->index }}">
                                                <label class="form-check-label" for="delete-{{ $loop->index }}">
                                                    Hapus gambar
                                                </label>
                                            </div>
                                            <input type="hidden" name="existing_images[]" value="{{ $image }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="link_youtube">Link Youtube</label>
                        <input type="url" name="link_youtube" id="link_youtube" class="form-control"
                            placeholder="https://www.youtube.com/watch?v=example"
                            value="{{ old('link_youtube', $produk->link_youtube ?? '') }}">
                    </div>

                    <div id="dynamicFieldsContainer">
                        <!-- Kolom dinamis akan ditampilkan di sini -->
                    </div>



                    <button type="submit" class="btn btn-primary">{{ isset($produk) ? 'Update' : 'Tambah' }}</button>
                    <a href="{{ route('kelola_data.produk.index') }}" class="btn btn-warning">Kembali</a>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahDataModalLabel">Tambah Data Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tambahDataForm">
                        @csrf
                        <input type="hidden" name="type" id="modalType">
                        <div class="form-group subkategori-field d-none">
                            <label for="modalKategori">Kategori</label>
                            <select class="form-select" name="kategori_id" id="modalKategori"
                                style="width: 100%"></select>
                        </div>
                        <div class="form-group arab-field">
                            <label for="modalNamaArab">Nama (Arab)</label>
                            <input type="text" name="nama_arab" id="modalNamaArab" class="form-control text-right"
                                dir="rtl" required>
                        </div>

                        <div class="form-group arab-field">
                            <label for="modalNamaIndonesia">Nama (Indonesia)</label>
                            <input type="text" name="nama_indonesia" id="modalNamaIndonesia" class="form-control"
                                required>
                        </div>

                        <!-- Field khusus untuk supplier -->
                        <div class="form-group supplier-field d-none">
                            <label for="modalNamaSupplier">Nama Supplier</label>
                            <input type="text" name="nama_supplier" id="modalNamaSupplier" class="form-control">
                        </div>

                        <div class="form-group supplier-field d-none">
                            <label for="modalAlamat">Alamat</label>
                            <textarea name="alamat" id="modalAlamat" class="form-control"></textarea>
                        </div>


                        <div class="form-group supplier-field d-none">
                            <label for="modalTelepon">Telepon</label>
                            <input type="text" name="telepon" id="modalTelepon" class="form-control">
                        </div>

                        <div class="form-group supplier-field d-none">
                            <label for="modalEmail">Email</label>
                            <input type="text" name="email" id="modalEmail" class="form-control">
                        </div>

                        <!-- Field khusus untuk ukuran -->
                        <div class="form-group ukuran-field d-none">
                            <label for="modalUkuran">Ukuran</label>
                            <input type="text" name="ukuran" id="modalUkuran" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="simpanDataModal">
                        <span class="btn-text">Simpan</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.produkData = @json(isset($produk) ? $produk : null);

        function addMoreImageField() {
            const container = document.getElementById('imageUploadContainer');
            const newField = document.createElement('div');
            newField.className = 'input-group mb-3';
            newField.innerHTML = `
                <input type="file" name="images[]" class="form-control" accept="image/*">
                <button type="button" class="btn btn-danger remove-btn" onclick="removeImageField(this)">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            container.appendChild(newField);
        }

        function removeImageField(button) {
            const fieldGroup = button.parentElement;
            fieldGroup.remove();
        }
    </script>
@endsection
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Perkecil tulisan di item yang sudah dipilih */
        .select2-selection__choice {
            font-size: 11px !important;
            padding: 2px 6px !important;
            margin: 2px 4px 2px 0 !important;
            white-space: normal !important;
            line-height: 1.2 !important;
        }

        /* Perkecil tinggi box Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        /* Perkecil dropdown list */
        .select2-results__option {
            font-size: 12px !important;
            padding: 4px 8px !important;
        }

        /* Rapikan item Arab (RTL) */
        .select2-container .select2-selection__choice__display {
            direction: rtl !important;
            text-align: right !important;
        }

        .select2-selection__choice {
            display: none !important;
        }

        /* Wadah pilihan lebih rapat dan tidak melebar */
        .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 2px !important;
            padding: 2px 4px !important;
        }

        .select2-container {
            flex: 1 !important;
        }

        .input-group .select2-container .select2-selection {
            height: calc(2.25rem + 2px) !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        .input-group .btn {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
    </style>
@endpush
@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Function untuk buka modal dengan tambahan kategori untuk subkategori
        // Function untuk buka modal dengan tambahan kategori untuk subkategori
        function bukaModalTambah(type) {
            $('#modalType').val(type);
            $('#tambahDataModalLabel').text('Tambah ' + type.replace(/_/g, ' ').toUpperCase());

            // Sembunyikan semua field khusus
            $('.arab-field, .supplier-field, .ukuran-field, .subkategori-field').addClass('d-none');

            // Tampilkan field yang sesuai
            if (type !== 'supplier' && type !== 'ukuran') {
                $('.arab-field').removeClass('d-none');
            }

            if (type === 'supplier') {
                $('.supplier-field').removeClass('d-none');
            }

            if (type === 'ukuran') {
                $('.ukuran-field').removeClass('d-none');
            }

            // Tampilkan field kategori jika yang ditambah adalah sub_kategori
            if (type === 'sub_kategori') {
                $('.subkategori-field').removeClass('d-none');
                loadKategoriOptions();
            }

            // Reset form
            $('#tambahDataForm')[0].reset();
            $('#tambahDataModal').modal('show');
        }

        // Handle simpan data modal dengan loading
        $('#simpanDataModal').click(function() {
            const type = $('#modalType').val();
            const formData = new FormData($('#tambahDataForm')[0]);

            // Tampilkan loading, sembunyikan teks
            $(this).prop('disabled', true);
            $(this).find('.btn-text').addClass('d-none');
            $(this).find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: "{{ route('kelola_data.produk.tambahData') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Tambahkan opsi baru ke select2 yang sesuai
                        if (type !== 'supplier' && type !== 'ukuran') {
                            const newOption = new Option(response.data.nama_arab, response.data
                                .nama_arab, true, true);
                            $('#' + type).append(newOption).trigger('change');

                            // Update field Indonesia jika ada
                            if ($('#' + type + '_indonesia').length) {
                                $('#' + type + '_indonesia').val(response.data.nama_indonesia);
                            }
                        } else if (type === 'supplier') {
                            const newOption = new Option(response.data.text, response.data.id,
                                true,
                                true);
                            $('#supplier').append(newOption).trigger('change');
                        } else if (type === 'ukuran') {
                            const newOption = new Option(response.data.ukuran, response.data
                                .ukuran,
                                true, true);
                            $('#ukuran').append(newOption).trigger('change');
                        }

                        $('#tambahDataModal').modal('hide');

                        // Tampilkan toast sukses
                        toastr.success('Data berhasil ditambahkan');
                    }

                    // Kembalikan tombol ke keadaan semula
                    $('#simpanDataModal').prop('disabled', false);
                    $('#simpanDataModal').find('.btn-text').removeClass('d-none');
                    $('#simpanDataModal').find('.spinner-border').addClass('d-none');
                },
                error: function(xhr) {
                    // Tampilkan toast error
                    toastr.error('Gagal menambahkan data: ' + (xhr.responseJSON?.message ||
                        'Terjadi kesalahan'));

                    // Kembalikan tombol ke keadaan semula
                    $('#simpanDataModal').prop('disabled', false);
                    $('#simpanDataModal').find('.btn-text').removeClass('d-none');
                    $('#simpanDataModal').find('.spinner-border').addClass('d-none');
                }
            });
        });

        // Function untuk memuat opsi kategori
        function loadKategoriOptions() {
            $('#modalKategori').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Kategori",
                minimumInputLength: 0,
                dropdownParent: $('#tambahDataModal'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getkategori1') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama_arab + ' | ' + item.nama_indonesia,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        }



        $(document).ready(function() {



            const select2Fields = ['kategori', 'sub_kategori', 'penerbit', 'cover', 'kertas', 'kualitas',
                'harakat',
                'penulis', 'supplier', 'ukuran'
            ];

            select2Fields.forEach(function(field) {
                // Buat wrapper dan tombol
                const wrapper = $('#' + field).parent();
                wrapper.addClass('input-group');

                if (!wrapper.find('.btn-success').length) {
                    const button = $(`<button type="button" class="btn btn-success" onclick="bukaModalTambah('${field}')">
            <i class="fas fa-plus"></i>
        </button>`);

                    wrapper.append(button);
                }
            });
            // Load kolom dinamis saat halaman dimuat
            loadDynamicFields();

            function loadDynamicFields() {
                $.get("{{ route('kelola_data.produk.getDynamicColumns') }}", function(data) {
                    let container = $('#dynamicFieldsContainer');
                    container.empty();

                    if (data.success && data.columns.length > 0) {
                        data.columns.forEach(function(column) {
                            // Skip kolom yang sudah ada di form utama
                            if (['judul', 'kategori', 'penerbit', 'cover', 'kertas',
                                    'kualitas',
                                    'harakat', 'penulis', 'supplier', 'halaman', 'berat',
                                    'ukuran',
                                    'harga_modal',
                                    'harga_jual', 'stok', 'kd_produk', 'id', 'created_at',
                                    'updated_at', 'deleted_at'
                                ].includes(column)) {
                                return;
                            }

                            // Get existing values from PHP data
                            let arabValue = '';
                            let indoValue = '';

                            if (window.produkData) {
                                arabValue = window.produkData[column] || '';
                                indoValue = window.produkData[column + '_indo'] || '';
                            }

                            // Tambahkan field untuk kolom dinamis
                            container.append(`
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="${column}">${column.replace(/_/g, ' ').toUpperCase()} (Arab)</label>
                                            <input type="text" name="${column}" id="${column}" class="form-control text-right" dir="rtl"
                                                value="${arabValue}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="${column}_indo">${column.replace(/_/g, ' ').toUpperCase()} (Indonesia)</label>
                                            <input type="text" name="${column}_indo" id="${column}_indo" class="form-control"
                                                value="${indoValue}">
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    }
                }).fail(function() {
                    console.error('Gagal memuat kolom dinamis');
                });
            }
        });
    </script>

    <script>
        // Ganti fungsi renderSelected yang ada dengan versi baru ini
        function renderSelected(containerId, dataList, selectId) {
            let html = '';
            let indonesiaValues = [];

            dataList.forEach(item => {
                let arab = item.nama_arab || item.text?.split('|')[0]?.trim() || item.id || '';
                let indo = item.nama_indonesia || item.text?.split('|')[1]?.trim() || '';

                // Kumpulkan nilai Indonesia
                if (indo) {
                    indonesiaValues.push(indo);
                }

                html += `
        <span class="badge bg-primary d-inline-flex align-items-center me-1 mb-1"
              style="font-size:12px; padding:4px 6px; border-radius:6px;">

            <span style="margin-right:6px;">
                ${arab}
                <span style="color:#ddd"> | </span>
                ${indo}
            </span>

            <span class="remove-chip"
                  data-id="${arab}"
                  data-indonesia="${indo}"
                  data-select="${selectId}"
                  style="cursor:pointer; font-weight:bold; margin-left:2px;">
                  ×
            </span>
        </span>
    `;
            });

            // Update field Indonesia dengan nilai yang terkumpul
            if (selectId === 'kategori') {
                $("#kategori_indonesia").val(indonesiaValues.join(', '));
            } else if (selectId === 'sub_kategori') {
                $("#sub_kategori_indonesia").val(indonesiaValues.join(', '));
            }

            $("#" + containerId).html(html);
        }

        // Tambahkan fungsi ini di dalam $(document).ready()
        function updateIndonesiaField(selectId, targetFieldId) {
            let $select = $('#' + selectId);
            let list = $select.select2('data');
            let indonesiaValues = [];

            list.forEach(item => {
                let indo = item.nama_indonesia || item.text?.split('|')[1]?.trim() || '';
                if (indo) {
                    indonesiaValues.push(indo);
                }
            });

            $('#' + targetFieldId).val(indonesiaValues.join(', '));
        }

        $(document).ready(function() {

            // Inisialisasi saat halaman dimuat
            @if (isset($kategori))
                // Isi input Indonesia
                $("#kategori_indonesia").val(
                    @json($kategori).map(i => i.nama_indonesia).join(', ')
                );

                // Render selected kategori
                renderSelected('selected_kategori', @json($kategori), 'kategori');
            @endif

            @if (isset($subKategori))
                $("#sub_kategori_indonesia").val(
                    @json($subKategori).map(i => i.nama_indonesia).join(', ')
                );

                // Render selected sub kategori
                renderSelected('selected_sub_kategori', @json($subKategori), 'sub_kategori');
            @endif

            // Inisialisasi Select2 dengan tema Bootstrap 5
            $('.select2').select2({
                placeholder: 'Pilih opsi',
                allowClear: true,
                theme: 'bootstrap-5',
                dropdownParent: $('.card-body') // Memastikan dropdown muncul di dalam card
            });

            // Function untuk format angka
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Function untuk membersihkan format angka
            function cleanNumber(number) {
                return number.replace(/\./g, '');
            }

            // Handle harga modal input
            $('#harga_modal_display').on('input', function() {
                let value = cleanNumber($(this).val());
                if (value !== '') {
                    $('#harga_modal').val(value);
                    $(this).val(formatNumber(value));
                } else {
                    $('#harga_modal').val('');
                    $(this).val('');
                }
            });

            // Handle harga jual input
            $('#harga_jual_display').on('input', function() {
                let value = cleanNumber($(this).val());
                if (value !== '') {
                    $('#harga_jual').val(value);
                    $(this).val(formatNumber(value));
                } else {
                    $('#harga_jual').val('');
                    $(this).val('');
                }
            });

            // Function untuk menghitung laba
            function calculateProfit() {
                const hargaModal = parseInt(cleanNumber($('#harga_modal').val()) || 0);
                const hargaJual = parseInt(cleanNumber($('#harga_jual').val()) || 0);
                const laba = hargaJual - hargaModal;

                $('#laba').val(laba);
                $('#laba_display').val(formatNumber(laba));
            }

            // Panggil calculateProfit saat harga modal/jual berubah
            $('#harga_modal_display, #harga_jual_display').on('input', function() {
                calculateProfit();
            });

            // Juga panggil saat halaman dimuat untuk mengisi nilai awal
            $(document).ready(function() {
                calculateProfit();
            });

            // Replace the kategori select2 initialization with this:
            $('#kategori').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Pilih kategori",
                multiple: true,
                ajax: {
                    url: "{{ route('kelola_data.produk.getkategori') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: params => ({
                        q: $.trim(params.term),
                        _token: "{{ csrf_token() }}"
                    }),
                    processResults: data => ({
                        results: $.map(data, item => ({
                            id: item.id,
                            text: item.text,
                            nama_arab: item.nama_arab,
                            nama_indonesia: item.nama_indonesia
                        }))
                    })
                },
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    return `<div style="direction: rtl;text-align:right">${data.nama_arab} | <span style="color:#777">${data.nama_indonesia}</span></div>`;
                },
                escapeMarkup: m => m
            });


            // Ganti fungsi renderSelected yang ada dengan ini:




            $('#kategori').on('change', function() {
                let list = $('#kategori').select2('data');
                renderSelected('selected_kategori', list, 'kategori');
            });

            // Update event handler sub kategori
            $('#sub_kategori').on('change', function() {
                let list = $('#sub_kategori').select2('data');
                renderSelected('selected_sub_kategori', list, 'sub_kategori');
            });


            let kategoriAwal = @json($kategori ? array_column($kategori, 'nama_arab') : []);
            $('#kategori').on('change', function() {

                let selectedKategori = $(this).val();
                if (JSON.stringify(selectedKategori) === JSON.stringify(kategoriAwal)) {
                    return; // JANGAN RESET sub kategori
                }

                updateKategoriIndonesia();



                // 🔥 Jika tidak ada kategori → tidak load sub kategori
                if (!selectedKategori || selectedKategori.length === 0) {
                    return;
                }

                loadSubKategori(selectedKategori); // load ulang sesuai kategori terbaru
            });


            function updateKategoriIndonesia() {
                let texts = $('#kategori').select2('data').map(i => i.nama_indonesia);
                $('#kategori_indonesia').val(texts.join(', '));
            }


            function loadSubKategori(kategoriArray) {

                // Reset Select2 dengan benar
                $('#sub_kategori').select2('destroy');
                $('#sub_kategori').empty();

                $('#sub_kategori').select2({
                    theme: "bootstrap-5",
                    width: "100%",
                    placeholder: "Pilih sub kategori",
                    multiple: true,
                    ajax: {
                        url: "{{ route('kelola_data.produk.getsubkategori') }}",
                        dataType: 'json',
                        type: "POST",
                        delay: 250,
                        data: params => ({
                            q: params.term,
                            kategori: kategoriArray,
                            _token: "{{ csrf_token() }}"
                        }),
                        processResults: data => ({
                            results: $.map(data, item => ({
                                id: item.id,
                                text: item.nama_arab + ' | ' + item.nama_indonesia,
                                nama_arab: item.nama_arab,
                                nama_indonesia: item.nama_indonesia
                            }))
                        })
                    },
                    templateResult: d => {
                        if (d.loading) return d.text;
                        return `<div style="direction: rtl;text-align:right">${d.nama_arab} | <span style="color:#777">${d.nama_indonesia}</span></div>`;
                    },
                    escapeMarkup: m => m
                });

            }



            $('#penerbit').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih penerbit",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getpenerbit') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }

                    if (data.nama_arab) {
                        return data.nama_arab;
                    }

                    return data.id;
                }
            });

            $('#sub_kategori').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Pilih sub kategori",
            });



            // Update the kategori selection handler
            $('#penerbit').on('select2:select', function(e) {
                var data = e.params.data;
                $('#penerbit_indonesia').val(data.nama_indonesia);
            });


            $('#cover').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Cover",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getcover') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }
                    if (data.nama_arab) {
                        return data.nama_arab;
                    }

                    return data.id;
                }
            });

            // Update the kategori selection handler
            $('#cover').on('select2:select', function(e) {
                var data = e.params.data;
                $('#cover_indonesia').val(data.nama_indonesia);
            });

            $('#kertas').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Kertas",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getkertas') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }

                    if (data.nama_arab) {
                        return data.nama_arab;
                    }
                    return data.id;
                }
            });

            $('#kertas').on('select2:select', function(e) {
                var data = e.params.data;
                $('#kertas_indonesia').val(data.nama_indonesia);
            });

            // Kualitas Select2
            $('#kualitas').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Kualitas",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getkualitas') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }
                    if (data.nama_arab) {
                        return data.nama_arab;
                    }
                    return data.id;
                }
            });

            $('#kualitas').on('select2:select', function(e) {
                var data = e.params.data;
                $('#kualitas_indonesia').val(data.nama_indonesia);
            });

            // Harakat Select2
            $('#harakat').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Harakat",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getharakat') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }
                    if (data.nama_arab) {
                        return data.nama_arab;
                    }
                    return data.id;
                }
            });

            $('#harakat').on('select2:select', function(e) {
                var data = e.params.data;
                $('#harakat_indonesia').val(data.nama_indonesia);
            });


            $('#penulis').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih penulis",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getpenulis') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.nama_arab,
                                    text: item.text,
                                    nama_arab: item.nama_arab,
                                    nama_indonesia: item.nama_indonesia
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var $result = $(
                        '<div style="text-align: right; direction: rtl;">' +
                        data.nama_arab + ' | ' +
                        '<span style="color: #999; margin-right: 5px;">' + data.nama_indonesia +
                        '</span>' +
                        '</div>'
                    );
                    return $result;
                },
                templateSelection: function(data) {
                    if (data.id === '') {
                        return data.text;
                    }
                    if (data.nama_arab) {
                        return data.nama_arab;
                    }
                    return data.id;
                }
            });

            $('#penulis').on('select2:select', function(e) {
                var data = e.params.data;
                $('#penulis_indonesia').val(data.nama_indonesia);
            });

            // Supplier Select2
            $('#supplier').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih Supplier",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getsupplier') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                }
                            })
                        };
                    },
                    cache: true
                },

            });

            $('#ukuran').select2({
                theme: "bootstrap-5",
                width: "100%",
                placeholder: "Silahkan Pilih ukuran",
                minimumInputLength: 0,
                dropdownParent: $('.card-body'),
                ajax: {
                    url: "{{ route('kelola_data.produk.getukuran') }}",
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: $.trim(params.term),
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                }
                            })
                        };
                    },
                    cache: true
                },

            });


            @if (isset($produk) && $produk->penerbit)
                var penerbitOption = new Option("{{ $produk->penerbit }}", "{{ $produk->penerbit }}", true,
                    true);
                $('#penerbit').append(penerbitOption).trigger('change');
            @endif

            @if (isset($produk) && $produk->cover)
                var coverOption = new Option("{{ $produk->cover }}", "{{ $produk->cover }}", true, true);
                $('#cover').append(coverOption).trigger('change');
            @endif

            @if (isset($produk) && $produk->kertas)
                var kertasOption = new Option("{{ $produk->kertas }}", "{{ $produk->kertas }}", true, true);
                $('#kertas').append(kertasOption).trigger('change');
            @endif

            @if (isset($produk) && $produk->kualitas)
                var kualitasOption = new Option("{{ $produk->kualitas }}", "{{ $produk->kualitas }}", true,
                    true);
                $('#kualitas').append(kualitasOption).trigger('change');
            @endif

            @if (isset($produk) && $produk->harakat)
                var harakatOption = new Option("{{ $produk->harakat }}", "{{ $produk->harakat }}", true, true);
                $('#harakat').append(harakatOption).trigger('change');
            @endif

            @if (isset($produk) && $produk->penulis)
                var penulisOption = new Option("{{ $produk->penulis }}", "{{ $produk->penulis }}", true, true);
                $('#penulis').append(penulisOption).trigger('change');
            @endif

        });

        // Ganti event handler remove-chip dengan versi baru ini
        $(document).on('click', '.remove-chip', function() {
            let id = $(this).data('id');
            let indoValue = $(this).data('indonesia');
            let selectId = $(this).data('select');

            // Hapus dari Select2
            let $select = $('#' + selectId);
            let selectedValues = $select.val() || [];

            // Filter out the id to be removed
            selectedValues = selectedValues.filter(val => val != id);

            // Update Select2
            $select.val(selectedValues).trigger('change');

            // Update field Indonesia dengan cara yang lebih aman
            let list = $select.select2('data');
            let indonesiaValues = [];

            // Kumpulkan nilai Indonesia dari item yang masih tersisa
            list.forEach(item => {
                let indo = item.nama_indonesia || item.text?.split('|')[1]?.trim() || '';
                if (indo) {
                    indonesiaValues.push(indo);
                }
            });

            // Update field Indonesia
            if (selectId === 'kategori') {
                $("#kategori_indonesia").val(indonesiaValues.join(', '));
                renderSelected('selected_kategori', list, 'kategori');
            } else if (selectId === 'sub_kategori') {
                $("#sub_kategori_indonesia").val(indonesiaValues.join(', '));
                renderSelected('selected_sub_kategori', list, 'sub_kategori');
            }
        });
    </script>
@endpush
