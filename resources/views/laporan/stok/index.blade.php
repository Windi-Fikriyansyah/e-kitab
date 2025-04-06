@extends('template.app')
@section('title', 'Laporan Stok')
@section('content')
    <div class="page-heading">
        <h3>Laporan Stok</h3>
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
                        <h5 class="card-title">Laporan Stok</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="products" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <style>
        .right-gap {
            margin-right: 10px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('laporan.stok.load') }}",
                    type: "POST",

                },
                pageLength: 10,
                searching: true,
                order: [[4, 'asc']],
                columns: [

                    { data: 'barcode' },
                    { data: 'name' },
                    { data: 'category' },
                    {
                        data: 'selling_price',
                        render: function(data) {
                            return data.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                        }
                    },
                    { data: 'stock' },
                    { data: 'satuan', className: 'text-center' }
                ],
                columnDefs: [
                    { className: "dt-head-center", targets: ['_all'] },
                    { className: "dt-body-center", targets: [0, 1, 2, 4, 5] }
                ]
            });
        });
    </script>
@endpush
