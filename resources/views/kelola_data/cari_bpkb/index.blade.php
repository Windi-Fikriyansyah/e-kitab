@extends('template.app')
@section('title', 'Kelola Data BPKB')
@section('content')
    <div class="page-heading">
        <h3>Pencarian Data BPKB</h3>
    </div>
    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="card-title">BPKB</h5>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Jenis Kendaraan</label>
                        <div class="col-sm-10">
                            <select class="form-select select_option" id="jenis" data-placeholder="Silahkan Pilih" autofocus>
                                <option value="" selected>Silahkan Pilih</option>
                                @foreach($jenis as $j)
                                    <option value="{{ $j->jenis }}">{{ $j->jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Merek</label>
                        <div class="col-sm-10">
                            <select class="form-select select_option"  id="merk" data-placeholder="Silahkan Pilih" disabled>
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Nomor Arsip Dokumen</label>
                        <div class="col-sm-10">
                            <select class="form-select select_option" name="nomorRegister" id="nomorRegister" data-placeholder="Silahkan Pilih" >
                                <option value="" selected>Silahkan Pilih</option>
                            </select>
                        </div>
                    </div>
                    <table class="table align-middle mb-0" id="bpkb" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nomor Arsip Dokumen</th>
                                <th>Nomor BPKB</th>
                                <th>Nomor Polisi</th>
                                <th>SKPD</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <style>
        .right-gap {
            margin-right: 10px
        }
    </style>

<script>
   $(document).ready(function () {
    // Setup CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    const table = $('#bpkb').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('kelola_data.caribpkb.load') }}",
            type: "POST",
            data: function (data) {
                data.jenis = $('#jenis').val(); // Filter jenis
                data.merk = $('#merk').val();   // Filter merk
                data.nomorRegister = $('#nomorRegister').val(); // Filter nomor register
            }
        },
        pageLength: 10,
        searching: true,
        columns: [
            { data: 'nomorRegister' },
            { data: 'nomorBpkb' },
            { data: 'nomorPolisi' },
            { data: 'kodeSkpd' },
            { data: 'aksi', className: 'text-center' }
        ],
        columnDefs: [
            { className: "dt-head-center", targets: ['_all'] },
            { className: "dt-body-center", targets: [0, 1, 2, 4] }
        ]
    });

    // Load Merk when Jenis changes
    $('#jenis').on('change', function () {
        const jenisId = $(this).val();
        $('#merk').prop('disabled', true).html('<option value="">Silahkan Pilih</option>');
        $('#nomorRegister').prop('disabled', true).html('<option value="">Silahkan Pilih</option>');

        if (jenisId) {
            $.ajax({
                url: '{{ route("kelola_data.caribpkb.merks") }}',
                type: 'GET',
                data: { jenis_id: jenisId },
                success: function (data) {
                    $('#merk').prop('disabled', false).html('<option value="">Silahkan Pilih</option>');
                    data.forEach(function (merk) {
                        $('#merk').append(`<option value="${merk.merk}">${merk.merk}</option>`);
                    });
                }
            });
        }

        // Reload nomorRegister with selected jenis
        loadNomorRegister(jenisId);
        // Reload DataTable with selected filters
        table.ajax.reload();
    });

    // Reload nomorRegister when Merk changes
    $('#merk').on('change', function () {
        const jenisId = $('#jenis').val();
        const merkId = $(this).val();

        // Reload nomorRegister based on jenis and merk
        loadNomorRegister(jenisId, merkId);
        // Reload DataTable with selected filters
        table.ajax.reload();
    });

    // Reload DataTable when Nomor Register changes
    $('#nomorRegister').on('change', function () {
        table.ajax.reload();
    });

    // Function to load nomorRegister dropdown
    function loadNomorRegister(jenisId = '', merkId = '') {
        $('#nomorRegister').prop('disabled', true).html('<option value="">Silahkan Pilih</option>');

        $('#nomorRegister').select2({
            theme: "bootstrap-5",
            width: "100%",
            placeholder: "Silahkan Pilih...",
            ajax: {
                url: "{{ route('kelola_data.caribpkb.load_bpkb') }}",
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    return {
                        q: $.trim(params.term),
                        jenis_id: jenisId,
                        merk_id: merkId
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map((bpkb) => ({
                            id: bpkb.nomorRegister,
                            text: `${bpkb.nomorPolisi} | ${bpkb.nomorBpkb}`
                        }))
                    };
                },
                cache: true
            }
        }).prop('disabled', false);
    }

    // Load nomorRegister initially (when no filters are selected)
    loadNomorRegister();
});

    </script>
@endpush
