@extends('template.app')
@section('title', 'Buat Transaksi Supplier')

@section('content')
    <div class="page-heading">
        <h3>Buat Transaksi Supplier</h3>
    </div>

    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">

                {{-- FORM --}}
                <form action="{{ route('transaksi.transaksi_supplier.store') }}" method="POST">
                    @csrf

                    {{-- Supplier --}}
                    <div class="mb-3">
                        <label>Supplier</label>
                        <select name="id_supplier" id="supplier" class="form-select select2" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($supplier as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol pilih produk --}}
                    <button type="button" id="btnPilihProduk" class="btn btn-primary mb-3" disabled>
                        Pilih Produk
                    </button>

                    {{-- TABEL PRODUK TERPILIH --}}
                    <table class="table table-bordered" id="selectedProdukTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    {{-- Hidden untuk array produk --}}
                    <input type="hidden" name="produk" id="produkInput">

                    {{-- Total --}}
                    <div class="mb-3">
                        <label>Total</label>
                        <input type="text" id="total_format" class="form-control" readonly>
                        <input type="hidden" id="total" name="total">
                    </div>
                    {{-- Resi --}}
                    <div class="mb-3">
                        <label>Resi</label>
                        <input type="text" class="form-control" id="resi" autocomplete="off" value="0">
                        <input type="hidden" name="resi" id="resi_hidden">

                    </div>

                    {{-- Fee --}}
                    <div class="mb-3">
                        <label>Fee</label>
                        <input type="text" class="form-control" id="fee" value="0">
                        <input type="hidden" name="fee" id="fee_hidden">
                    </div>





                    <button type="submit" class="btn btn-success" id="submitBtn">Simpan Transaksi</button>

                </form>

            </div>
        </div>
    </div>


    {{-- MODAL PRODUK --}}
    <div class="modal fade" id="modalProduk" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Pilih Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered" id="produkTable" width="100%">
                        <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Judul</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnTambahkanProduk">Tambahkan Produk</button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        let dtTable;

        $('#supplier').select2({
            placeholder: "-- Pilih Supplier --",
            allowClear: true,
            theme: "bootstrap-5",
            width: '100%'
        });

        $('#supplier').on('change', function() {
            let val = $(this).val();

            if (val) {
                $("#btnPilihProduk").prop("disabled", false);
            } else {
                $("#btnPilihProduk").prop("disabled", true);
            }
        });

        $("#btnPilihProduk").click(function() {
            let supplier = $("#supplier").val();

            if (!supplier) return;

            $("#modalProduk").modal('show');

            if ($.fn.DataTable.isDataTable('#produkTable')) {
                dtTable.destroy();
            }

            dtTable = $("#produkTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('transaksi.transaksi_supplier.getProdukDT') }}",
                    type: "POST",
                    data: function(d) {
                        d.id_supplier = $("#supplier").val();
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'judul'
                    },
                    {
                        data: 'harga_modal',
                        render: function(data) {
                            if (!data) return '-';

                            // format ke rupiah
                            return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                        }
                    }
                ]
            });
        });

        // EVENT: klik baris → toggle checkbox
        $(document).on('click', '#produkTable tbody tr', function(e) {

            // Cegah toggle saat klik checkbox langsung
            if ($(e.target).is('input[type=checkbox]')) return;

            let checkbox = $(this).find('.choose-product');

            // Toggle centang
            checkbox.prop('checked', !checkbox.prop('checked'));

            // Tambah highlight row yang dipilih
            $(this).toggleClass('row-selected');
        });


        let selectedProduk = [];

        $("#btnTambahkanProduk").click(function() {

            $(".choose-product:checked").each(function() {
                let id = $(this).data("id");
                let judul = $(this).data("judul");
                let harga = parseInt($(this).data("harga"));

                if (!selectedProduk.some(p => p.id === id)) {

                    selectedProduk.push({
                        id,
                        judul,
                        harga,
                        qty: 1,
                        total: harga
                    });

                    $("#selectedProdukTable tbody").append(`
                <tr data-id="${id}">
                    <td>${judul}</td>
                    <td>Rp ${harga.toLocaleString()}</td>

                    <td width="120">
                        <input type="number" min="1" class="form-control qty-input" data-id="${id}" value="1">
                    </td>

                    <td class="item-total" data-id="${id}">Rp ${harga.toLocaleString()}</td>

                    <td><button type="button" class="btn btn-danger btn-sm removeProduk">Hapus</button></td>
                </tr>
            `);
                }
            });

            updateTotal();
            $("#modalProduk").modal('hide');
        });


        $(document).on("input", ".qty-input", function() {

            let id = $(this).data("id");
            let qty = parseInt($(this).val());

            // Jika qty kosong, null, NaN → jadikan 0
            if (isNaN(qty) || qty < 1) qty = 0;

            $(this).val(qty);

            let item = selectedProduk.find(p => p.id == id);
            item.qty = qty;
            item.total = item.harga * qty;

            $(`.item-total[data-id="${id}"]`).text("Rp " + item.total.toLocaleString());

            updateTotal();
        });


        // Hapus produk
        $(document).on("click", ".removeProduk", function() {
            let id = $(this).closest("tr").data("id");
            selectedProduk = selectedProduk.filter(p => p.id !== id);

            $(this).closest("tr").remove();
            updateTotal();
        });

        function updateTotal() {
            let subtotal = selectedProduk.reduce((sum, p) => sum + p.total, 0);

            $("#total_format").val(formatRupiah(subtotal));
            $("#total").val(subtotal);

            // Simpan ke hidden input (produk)
            $("#produkInput").val(JSON.stringify(selectedProduk));
        }


        $("#fee").on("input", function() {
            let angka = toNumber($(this).val());
            $("#fee_hidden").val(angka);
            $(this).val(formatRupiah(angka));
        });

        $("#resi").on("input", function() {
            let angka = toNumber($(this).val());
            $("#resi_hidden").val(angka);
            $(this).val(formatRupiah(angka));
        });


        function formatRupiah(angka) {
            return "Rp " + angka.toLocaleString('id-ID');
        }

        function toNumber(rupiah) {
            return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
        }

        $("#submitBtn").click(function(e) {
            // Convert total numeric
            let totalNum = toNumber($("#total").val());
            $("#total").val(totalNum);

            // Jika resi_hidden kosong → isi 0
            if (!$("#resi_hidden").val()) {
                $("#resi_hidden").val(0);
            }
            if (!$("#fee_hidden").val()) {
                $("#fee_hidden").val(0);
            }

            // Pastikan produk tidak kosong
            if (selectedProduk.length === 0) {
                e.preventDefault();
                alert("Produk belum dipilih!");
                return;
            }
        });
    </script>
@endpush

@push('style')
    <style>
        .row-selected {
            background: #e8f5e9 !important;
            /* Hijau muda */
        }
    </style>
@endpush
