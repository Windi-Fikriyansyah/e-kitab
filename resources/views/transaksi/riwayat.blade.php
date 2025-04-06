<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 20px;
            margin: 20px auto;
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1.5rem;
        }

        .table thead th {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: #fff;
            font-weight: 500;
            border: none;
        }

        .table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table tbody td {
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        .badge-success {
            background-color: #1cc88a;
            padding: 8px 15px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .badge-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(28,200,138,0.4);
        }

        .card-header {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
        }

        .amount-col {
            font-family: 'Roboto Mono', monospace;
            font-weight: 500;
        }

        .date-col {
            color: #666;
            font-size: 0.9rem;
        }

        .product-col {
            font-weight: 500;
            color: #2e3a54;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .pagination {
            margin-top: 20px;
            justify-content: center;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .search-input {
            padding-left: 40px;
            border-radius: 30px;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        }

        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 15px;
            }
        }
        .pagination .page-link {
            color: #4e73df;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .pagination .page-link:hover {
            background-color: #e8eaf6;
            border-color: #4e73df;
            color: #4e73df;
        }

        /* Style untuk highlight hasil pencarian */
        .highlight {
            background-color: #fff3cd;
            padding: 2px;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="{{ route('transaksi.index') }}" class="btn btn-warning">Kembali</a>
        <div class="card table-container">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Riwayat Transaksi</h4>
                    <small class="text-white-50">Menampilkan semua transaksi Anda</small>
                </div>
                <span class="badge bg-white text-primary">
                    Total: {{ count($transactions) }}
                </span>
            </div>

            <div class="card-body">
                <form action="{{ route('transaksi.cariData') }}" method="GET" class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text"
                           name="search"
                           class="form-control search-input"
                           placeholder="Cari transaksi..."
                           value="{{ request('search') }}">
                </form>

                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="transactionTable">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <i class="fas fa-hashtag me-2"></i>No
                                </th>
                                <th scope="col">
                                    <i class="far fa-calendar-alt me-2"></i>Tanggal
                                </th>
                                <th scope="col">
                                    <i class="fas fa-box me-2"></i>Kode Transaksi
                                </th>
                                <th scope="col">
                                    <i class="fas fa-shopping-cart me-2"></i>Total Belanja
                                </th>
                                <th scope="col">
                                    <i class="fas fa-money-bill-wave me-2"></i>Total Bayar
                                </th>
                                <th scope="col">
                                    <i class="fas fa-info-circle me-2"></i>Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="date-col">
                                    <i class="far fa-calendar-alt me-2"></i>
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y') }}
                                </td>
                                <td class="product-col">{{ $transaction->transaction_code }}</td>
                                <td class="amount-col">
                                    Rp {{ number_format($transaction->TotalAmount, 0, ',', '.') }}
                                </td>
                                <td class="amount-col">
                                    Rp {{ number_format($transaction->PaymentAmount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle me-1"></i>Selesai
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>Tidak ada transaksi</h5>
                                    <p class="text-muted">
                                        @if(request('search'))
                                            Tidak ada hasil untuk pencarian "{{ request('search') }}"
                                        @else
                                            Belum ada riwayat transaksi yang tercatat
                                        @endif
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fungsi pencarian client-side
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchText = this.value.toLowerCase();
            const rows = document.querySelectorAll('#transactionTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });

            // Auto-submit setelah 3 karakter
            if (this.value.length >= 3 || this.value.length === 0) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>
