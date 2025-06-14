@extends('template.app')
@section('title', isset($generate_deskripsi) ? 'Edit Deskripsi' : 'Tambah Deskripsi')
@section('content')
    <div class="page-heading">
        <h3>{{ isset($generate_deskripsi) ? 'Edit Deskripsi' : 'Tambah Deskripsi' }}</h3>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-message-square-x'></i></div>
                    <div class="ms-3">
                        <ul class="mb-0 text-white">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('message'))
            <div
                class="alert alert-{{ session('message_type') ?? 'success' }} border-0 bg-{{ session('message_type') ?? 'success' }} alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <div class="font-35 text-white"><i class='bx bxs-check-circle'></i></div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">{{ session('message_title') ?? 'Success' }}</h6>
                        <div class="text-white">{{ session('message') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form id="generate_deskripsiForm"
                    action="{{ isset($generate_deskripsi) ? route('kelola_link.generate_deskripsi.update', Crypt::encrypt($generate_deskripsi->id)) : route('kelola_link.generate_deskripsi.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($generate_deskripsi))
                        @method('PUT')
                    @endif

                    <input type="hidden" name="id_produk" value="{{ $produk->id }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="kd_produk">Kode Produk</label>
                                <input type="text" name="kd_produk" id="kd_produk" class="form-control"
                                    value="{{ old('kd_produk', $produk->kd_produk ?? '') }}"
                                    placeholder="Masukkan kode produk" readonly>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="judul">Judul</label>
                                <input type="text" name="judul" id="judul" class="form-control"
                                    value="{{ old('judul', $produk->judul ?? '') }}" placeholder="Masukkan judul" readonly>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="supplier">Supplier</label>
                                <input type="text" name="nama_supplier" id="nama_supplier" class="form-control"
                                    value="{{ old('nama_supplier', $produk->nama_supplier ?? '') }}"
                                    placeholder="Masukkan supplier" readonly>
                            </div>
                        </div>

                        <!-- WhatsApp Section -->
                        <div class="row">
                            <h3>Whatsapp</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_wa">Pilih Format Arab (WhatsApp)</label>
                                    <select class="form-select @error('format_wa') is-invalid @enderror" name="format_wa"
                                        id="format_wa" style="width: 100%">
                                    </select>
                                    @error('format_wa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_arab" name="deskripsi_jadi_arab" rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_indonesia">Pilih Format Indonesia (WhatsApp)</label>
                                    <select class="form-select @error('format_indonesia') is-invalid @enderror"
                                        name="format_indonesia" id="format_indonesia" style="width: 100%">
                                    </select>
                                    @error('format_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_indonesia" name="deskripsi_jadi_indonesia" rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instagram Section -->
                        <div class="row mt-4">
                            <h3>Instagram</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_ig_arab">Pilih Format Arab (Instagram)</label>
                                    <select class="form-select @error('format_ig_arab') is-invalid @enderror"
                                        name="format_ig_arab" id="format_ig_arab" style="width: 100%">
                                    </select>
                                    @error('format_ig_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Instagram (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_ig_arab" name="deskripsi_jadi_ig_arab" rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_ig_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_ig_indonesia">Pilih Format Indonesia (Instagram)</label>
                                    <select class="form-select @error('format_ig_indonesia') is-invalid @enderror"
                                        name="format_ig_indonesia" id="format_ig_indonesia" style="width: 100%">
                                    </select>
                                    @error('format_ig_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Instagram (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_ig_indonesia" name="deskripsi_jadi_ig_indonesia" rows="5"
                                            readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_ig_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facebook Section -->
                        <div class="row mt-4">
                            <h3>Facebook</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_fb_arab">Pilih Format Arab (Facebook)</label>
                                    <select class="form-select @error('format_fb_arab') is-invalid @enderror"
                                        name="format_fb_arab" id="format_fb_arab" style="width: 100%">
                                    </select>
                                    @error('format_fb_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Facebook (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_fb_arab" name="deskripsi_jadi_fb_arab" rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_fb_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_fb_indonesia">Pilih Format Indonesia (Facebook)</label>
                                    <select class="form-select @error('format_fb_indonesia') is-invalid @enderror"
                                        name="format_fb_indonesia" id="format_fb_indonesia" style="width: 100%">
                                    </select>
                                    @error('format_fb_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Facebook (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_fb_indonesia" name="deskripsi_jadi_fb_indonesia" rows="5"
                                            readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_fb_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Threads Section -->
                        <div class="row mt-4">
                            <h3>Threads</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_threads_arab">Pilih Format Arab (Threads)</label>
                                    <select class="form-select @error('format_threads_arab') is-invalid @enderror"
                                        name="format_threads_arab" id="format_threads_arab" style="width: 100%">
                                    </select>
                                    @error('format_threads_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Threads (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_threads_arab" name="deskripsi_jadi_threads_arab" rows="5"
                                            readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_threads_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_threads_indonesia">Pilih Format Indonesia (Threads)</label>
                                    <select class="form-select @error('format_threads_indonesia') is-invalid @enderror"
                                        name="format_threads_indonesia" id="format_threads_indonesia"
                                        style="width: 100%">
                                    </select>
                                    @error('format_threads_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Threads (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_threads_indonesia" name="deskripsi_jadi_threads_indonesia"
                                            rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_threads_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- X (Twitter) Section -->
                        <div class="row mt-4">
                            <h3>X/Twitter</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_x_arab">Pilih Format Arab (X/Twitter)</label>
                                    <select class="form-select @error('format_x_arab') is-invalid @enderror"
                                        name="format_x_arab" id="format_x_arab" style="width: 100%">
                                    </select>
                                    @error('format_x_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi X/Twitter (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_x_arab" name="deskripsi_jadi_x_arab" rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_x_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_x_indonesia">Pilih Format Indonesia (X/Twitter)</label>
                                    <select class="form-select @error('format_x_indonesia') is-invalid @enderror"
                                        name="format_x_indonesia" id="format_x_indonesia" style="width: 100%">
                                    </select>
                                    @error('format_x_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi X/Twitter (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_x_indonesia" name="deskripsi_jadi_x_indonesia" rows="5"
                                            readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_x_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pinterest Section -->
                        <div class="row mt-4">
                            <h3>Pinterest</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_pinterest_arab">Pilih Format Arab (Pinterest)</label>
                                    <select class="form-select @error('format_pinterest_arab') is-invalid @enderror"
                                        name="format_pinterest_arab" id="format_pinterest_arab" style="width: 100%">
                                    </select>
                                    @error('format_pinterest_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Pinterest (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_pinterest_arab" name="deskripsi_jadi_pinterest_arab"
                                            rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_pinterest_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_pinterest_indonesia">Pilih Format Indonesia (Pinterest)</label>
                                    <select class="form-select @error('format_pinterest_indonesia') is-invalid @enderror"
                                        name="format_pinterest_indonesia" id="format_pinterest_indonesia"
                                        style="width: 100%">
                                    </select>
                                    @error('format_pinterest_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Pinterest (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_pinterest_indonesia" name="deskripsi_jadi_pinterest_indonesia"
                                            rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_pinterest_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <h3>Marketplace</h3>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_marketplace_arab">Pilih Format Arab (Marketplace)</label>
                                    <select class="form-select @error('format_marketplace_arab') is-invalid @enderror"
                                        name="format_marketplace_arab" id="format_marketplace_arab" style="width: 100%">
                                    </select>
                                    @error('format_marketplace_arab')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Marketplace (Arab)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_marketplace_arab" name="deskripsi_jadi_marketplace_arab"
                                            rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_marketplace_arab">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format_marketplace_indonesia">Pilih Format Indonesia (Marketplace)</label>
                                    <select
                                        class="form-select @error('format_marketplace_indonesia') is-invalid @enderror"
                                        name="format_marketplace_indonesia" id="format_marketplace_indonesia"
                                        style="width: 100%">
                                    </select>
                                    @error('format_marketplace_indonesia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi Jadi Marketplace (Indonesia)</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="deskripsi_jadi_marketplace_indonesia" name="deskripsi_jadi_marketplace_indonesia"
                                            rows="5" readonly></textarea>
                                        <button class="btn btn-outline-secondary copy-btn" type="button"
                                            data-target="deskripsi_jadi_marketplace_indonesia">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('kelola_link.generate_deskripsi.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ isset($generate_deskripsi) ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            @if (isset($generate_deskripsi))
                $(document).ready(function() {
                    // WhatsApp
                    $('#deskripsi_jadi_arab').val(`{!! $generate_deskripsi->deskripsi_wa_arab !!}`);
                    $('#deskripsi_jadi_indonesia').val(`{!! $generate_deskripsi->deskripsi_wa_indonesia !!}`);

                    // Instagram
                    $('#deskripsi_jadi_ig_arab').val(`{!! $generate_deskripsi->deskripsi_ig_arab !!}`);
                    $('#deskripsi_jadi_ig_indonesia').val(`{!! $generate_deskripsi->deskripsi_ig_indonesia !!}`);

                    // Facebook
                    $('#deskripsi_jadi_fb_arab').val(`{!! $generate_deskripsi->deskripsi_fb_arab !!}`);
                    $('#deskripsi_jadi_fb_indonesia').val(`{!! $generate_deskripsi->deskripsi_fb_indonesia !!}`);

                    // Threads
                    $('#deskripsi_jadi_threads_arab').val(`{!! $generate_deskripsi->deskripsi_threads_arab !!}`);
                    $('#deskripsi_jadi_threads_indonesia').val(`{!! $generate_deskripsi->deskripsi_threads_indonesia !!}`);

                    // X/Twitter
                    $('#deskripsi_jadi_x_arab').val(`{!! $generate_deskripsi->deskripsi_x_arab !!}`);
                    $('#deskripsi_jadi_x_indonesia').val(`{!! $generate_deskripsi->deskripsi_x_indonesia !!}`);

                    // Pinterest
                    $('#deskripsi_jadi_pinterest_arab').val(`{!! $generate_deskripsi->deskripsi_pinterest_arab !!}`);
                    $('#deskripsi_jadi_pinterest_indonesia').val(`{!! $generate_deskripsi->deskripsi_pinterest_indonesia !!}`);

                    $('#deskripsi_jadi_marketplace_arab').val(`{!! $generate_deskripsi->deskripsi_marketplace_arab !!}`);
                    $('#deskripsi_jadi_marketplace_indonesia').val(`{!! $generate_deskripsi->deskripsi_marketplace_indonesia !!}`);
                });
            @endif
            $(document).ready(function() {
                // Inisialisasi Select2 dengan tema Bootstrap 5
                $('.select2').select2({
                    placeholder: 'Pilih opsi',
                    allowClear: true,
                    theme: 'bootstrap-5',
                    dropdownParent: $('.card-body')
                });

                // Function to initialize platform select2
                function initPlatformSelect(selectId, language, platform) {
                    $(selectId).select2({
                        theme: "bootstrap-5",
                        width: "100%",
                        placeholder: `Silahkan Pilih Format ${platform} ${language === 2 ? 'Arab' : 'Indonesia'}`,
                        minimumInputLength: 0,
                        dropdownParent: $('.card-body'),
                        ajax: {
                            url: "{{ route('kelola_link.generate_deskripsi.getformat') }}",
                            dataType: 'json',
                            type: "POST",
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: $.trim(params.term),
                                    language: language,
                                    platform: platform.toLowerCase(),
                                    _token: "{{ csrf_token() }}"
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: $.map(data, function(item) {
                                        return {
                                            id: item.id,
                                            text: item.nama_format,
                                            format: item.format_deskripsi
                                        }
                                    })
                                };
                            },
                            cache: true
                        }
                    });

                    // Handle select event
                    $(selectId).on('select2:select', function(e) {
                        var formatData = e.params.data;
                        var produkId = '{{ $produk->id }}';
                        var textareaId = $(this).attr('id').replace('format_', 'deskripsi_jadi_');

                        // Determine which API to call based on language
                        var apiUrl = language === 2 ?
                            "{{ route('kelola_link.generate_deskripsi.get_produk_detail') }}" :
                            "{{ route('kelola_link.generate_deskripsi.get_produk_Indo_detail') }}";

                        // Ambil data produk
                        $.get(apiUrl, {
                            id: produkId
                        }, function(produk) {
                            // Ganti placeholder dengan data produk (using regex with g flag for global replacement)
                            var deskripsiJadi = formatData.format
                                .replace(/{judul}/g, language === 2 ? produk.judul : produk
                                    .judul_indo || '')
                                .replace(/{cover}/g, language === 2 ? produk.cover : produk
                                    .cover_indo || '')
                                .replace(/{kertas}/g, language === 2 ? produk.kertas :
                                    produk
                                    .kertas_indo || '')
                                .replace(/{kualitas}/g, language === 2 ? produk.kualitas :
                                    produk
                                    .kualitas_indo || '')
                                .replace(/{kategori}/g, language === 2 ? produk.kategori :
                                    produk
                                    .kategori_indo || '')
                                .replace(/{penerbit}/g, language === 2 ? produk.penerbit :
                                    produk
                                    .penerbit_indo || '')
                                .replace(/{judul}/g, language === 2 ? produk.judul : produk
                                    .judul_indo || '')
                                .replace(/{harakat}/g, language === 2 ? produk.harakat :
                                    produk.harakat_indo || '')
                                .replace(/{penulis}/g, language === 2 ? produk.penulis :
                                    produk.penulis_indo || '')
                                .replace(/{ukuran}/g, produk.ukuran || '')
                                .replace(/{halaman}/g, produk.halaman || '')
                                .replace(/{berat}/g, produk.berat ? produk.berat + ' gram' :
                                    '')
                                .replace(/{stok}/g, produk.stok || '')
                                .replace(/{supplier}/g, produk.nama_supplier || '');

                            // Tampilkan di textarea
                            $(`#${textareaId}`).val(deskripsiJadi);
                        });
                    });
                }

                // Initialize all platform selects
                // WhatsApp
                initPlatformSelect('#format_wa', 2, 'WhatsApp');
                initPlatformSelect('#format_indonesia', 1, 'WhatsApp');

                // Instagram
                initPlatformSelect('#format_ig_arab', 2, 'Instagram');
                initPlatformSelect('#format_ig_indonesia', 1, 'Instagram');

                // Facebook
                initPlatformSelect('#format_fb_arab', 2, 'Facebook');
                initPlatformSelect('#format_fb_indonesia', 1, 'Facebook');

                // Threads
                initPlatformSelect('#format_threads_arab', 2, 'Threads');
                initPlatformSelect('#format_threads_indonesia', 1, 'Threads');

                // X/Twitter
                initPlatformSelect('#format_x_arab', 2, 'X');
                initPlatformSelect('#format_x_indonesia', 1, 'X');

                // Pinterest
                initPlatformSelect('#format_pinterest_arab', 2, 'Pinterest');
                initPlatformSelect('#format_pinterest_indonesia', 1, 'Pinterest');

                initPlatformSelect('#format_marketplace_arab', 2, 'Marketplace');
                initPlatformSelect('#format_marketplace_indonesia', 1, 'Marketplace');

                // Copy button functionality
                $('.copy-btn').click(function() {
                    var targetId = $(this).data('target');
                    var textarea = document.getElementById(targetId);
                    textarea.select();
                    document.execCommand('copy');

                    // Show tooltip feedback
                    $(this).tooltip({
                        title: 'Copied!',
                        trigger: 'manual'
                    }).tooltip('show');

                    // Hide tooltip after 1 second
                    setTimeout(function() {
                        $('.copy-btn').tooltip('hide');
                    }, 1000);
                });
            });
        });
    </script>
@endpush
