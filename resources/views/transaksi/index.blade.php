<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 1024px) {
            .grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .product-item {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: box-shadow 0.2s;
        }

        .product-item:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .product-item h3 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-item p {
            color: #4b5563;
        }

        /* Cart Section */
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .item-info h3 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .item-info p {
            color: #4b5563;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-button {
            padding: 0.25rem;
            border-radius: 9999px;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .control-button:hover {
            background: #e5e7eb;
        }

        .delete-button {
            color: #ef4444;
        }

        .quantity {
            width: 2rem;
            text-align: center;
        }

        .cart-total {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .total-box {
            background-color: #f8f9fa;
            /* Warna kotak */
            border: 2px solid #007bff;
            /* Border biru */
            border-radius: 10px;
            /* Sudut melengkung */
            padding: 20px 40px;
            /* Ruang di dalam kotak */
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Bayangan */
        }

        .total-box .label {
            font-size: 18px;
            /* Ukuran font label */
            font-weight: bold;
            color: #333;
            /* Warna teks label */
        }

        .total-box .amount {
            font-size: 28px;
            /* Ukuran font total */
            font-weight: bold;
            color: #007bff;
            /* Warna teks total */
            margin-left: 10px;
            /* Jarak antara label dan total */
        }


        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .label {
            font-weight: 600;
        }

        .amount {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .payment-button {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .payment-button:hover {
            background: #1d4ed8;
        }


        .payment-button1 {
            width: 100%;
            background: #ef4444;
            ;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .payment-button1:hover {
            background: #dc2626;
        }

        /* Icons */
        .lucide {
            width: 1.25rem;
            height: 1.25rem;
        }

        .search-container {
            margin-bottom: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #2563eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            outline: none;
        }

        .search-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .quantity-input {
            width: 3rem;
            text-align: center;
            padding: 0.25rem;
            border-radius: 0.25rem;
            border: 1px solid #ddd;
            font-size: 1rem;
        }

        .payment-section {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .payment-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #2563eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            outline: none;
        }

        .payment-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-row span {
            font-weight: 600;
        }

        .payment-button {
            width: 100%;
            background: #2563eb;
            color: white;
            padding: 0.75rem;
            border-radius: 0.5rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .payment-button:hover {
            background: #1d4ed8;
        }

        .header {
            display: flex;
            justify-content: right;
            align-items: center;
            margin-bottom: 2rem;
        }

        .home-button {

            margin-left: 10px;
            /* Adjust spacing between buttons */
            text-decoration: none;
            color: #000;
            /* Set color as needed */
        }

        .logo {
            height: 50px;
            /* Adjust based on your logo size */
        }

        .home-button {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .home-button:hover {
            background-color: #1d4ed8;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            /* Slightly darker overlay */
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
            /* Add slight blur effect to background */
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            /* More rounded corners */
            width: 90%;
            max-width: 800px;
            /* Wider modal */
            max-height: 90%;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            /* More pronounced shadow */
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            /* Smooth entrance animation */
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content .card {
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .search-container {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #2563eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .product-item {
            background: #f9fafb;
            padding: 1.25rem;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #2563eb;
        }

        .close-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #ef4444;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .close-button:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .close-button i {
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">


        <div class="grid">
            <!-- Products Section -->
            <div class="products-section">
                <div class="card">
                    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h2>Products</h2>
                        <div>
                            <a href="{{ route('dashboard') }}" class="home-button">Home</a>
                            <a href="{{ route('transaksi.riwayat') }}" class="home-button">Riwayat Transaksi</a>
                            <button id="showModalButton" class="home-button">Pilih produk</button>
                        </div>
                    </div>

                    <div class="products-section">
                        <button class="payment-button1" id="clearAllButton" onclick="clearAllItems()">Clear All</button>
                        <div class="cart-items" id="cartItems">
                            <!-- Cart items akan diisi oleh JavaScript -->
                        </div>
                    </div>

                </div>
            </div>

            <!-- Cart Section -->
            <div class="cart-section">
                <div class="card">


                    <div class="cart-total">
                        <div class="total-box">
                            <span class="label">Total:</span>
                            <span class="amount" id="totalAmount">Rp 0</span>
                        </div>
                    </div>


                    <div class="payment-section">
                        <div>
                            <label for="paymentAmount">Nominal Pembayaran:</label>
                            <input type="text" id="paymentAmount" class="payment-input"
                                placeholder="Masukkan Nominal Pembayaran" oninput="formatCurrency(this)">
                        </div>
                        <div>
                            <label for="changeAmount">Kembalian:</label>
                            <input type="text" id="changeAmount" class="payment-input" placeholder="Kembalian"
                                disabled>
                        </div>

                        <button class="payment-button" onclick="processTransaction()">
                            <i data-lucide="credit-card"></i>
                            <span>Bayar & Cetak</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div id="productModal" class="modal">
        <div class="modal-content">
            <button class="close-button" id="closeModalButton">Tutup</button>
            <div class="card">
                <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Products</h2>
                </div>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search products..." id="searchInput"
                        onkeyup="searchProducts(this.value)">
                </div>
                <div class="products-grid" id="productsGrid">
                    @foreach ($products as $product)
                        <div class="product-item" onclick="addToCart({{ $product->id }})"
                            data-product-name="{{ strtolower($product->name) }}">
                            <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}"
                                style="width:100%; border-radius:0.5rem;">
                            <h3>{{ $product->name }}</h3>
                            <p>Rp. {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.getElementById("showModalButton").addEventListener("click", function() {
                document.getElementById("productModal").style.display = "flex";
            });

            // Tutup modal
            document.getElementById("closeModalButton").addEventListener("click", function() {
                document.getElementById("productModal").style.display = "none";
            });

            // Fungsi pencarian produk
            function searchProducts(query) {
                const products = document.querySelectorAll('.product-item');
                products.forEach(product => {
                    const name = product.getAttribute('data-product-name');
                    if (name.includes(query.toLowerCase())) {
                        product.style.display = "block";
                    } else {
                        product.style.display = "none";
                    }
                });
            }

            function clearAllItems() {
                cart = []; // Empty the cart array
                renderCart(); // Re-render the cart to show it is empty
            }


            function searchProducts(query) {
                fetch(`/transaksi/products/search?search=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(products => {
                        const productsGrid = document.getElementById('productsGrid');
                        productsGrid.innerHTML = products.map(product => `
                <div class="product-item" onclick="addToCart(${product.id})">
                    <img src="/storage/${product.photo}" alt="${product.name}" style="width:100%; border-radius:0.5rem;">
                    <h3>${product.name}</h3>
                    <p>Rp. ${new Intl.NumberFormat('id-ID').format(product.selling_price)}</p>
                </div>
            `).join('');
                    });
            }

            // Add debouncing to prevent too many requests
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchProducts(e.target.value);
                }, 300);
            });

            lucide.createIcons();

            let cart = [];

            function formatselling_price(selling_price) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(selling_price);
            }

            function addToCart(productId) {
                fetch(`/transaksi/products/${productId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal memuat detail produk');
                        }
                        return response.json();
                    })
                    .then(product => {
                        // Cek apakah stok habis
                        if (product.stock <= 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Stok Habis',
                                text: 'Barang ini habis stok dan tidak bisa ditambahkan ke keranjang.',
                            });
                            return; // Hentikan proses
                        }

                        // Tambahkan ke keranjang tanpa SweetAlert
                        const cartItem = cart.find(item => item.id === product.id);
                        if (cartItem) {
                            cartItem.quantity += 1;
                        } else {
                            cart.push({
                                ...product,
                                quantity: 1
                            });
                        }

                        // Render ulang keranjang
                        renderCart();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `Produk "${product.name}" berhasil ditambahkan ke keranjang.`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
                    .catch(error => {
                        console.error('Gagal memuat detail produk:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal memuat detail produk. Silakan coba lagi.',
                        });
                    });
            }





            function renderCart() {
                const cartItems = document.getElementById('cartItems');
                cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="item-info">
                        <h3>${item.name}</h3>
                        <p>${formatselling_price(item.selling_price)}</p>
                    </div>
                    <div class="item-controls">
                        <button class="control-button" onclick="updateQuantity(${item.id}, ${item.quantity - 1})"><i data-lucide="minus"></i></button>
                        <input type="number" class="quantity-input" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)">

                        <button class="control-button" onclick="updateQuantity(${item.id}, ${item.quantity + 1})"><i data-lucide="plus"></i></button>
                        <button class="control-button delete-button" onclick="removeFromCart(${item.id})"><i data-lucide="trash-2"></i></button>
                    </div>
                </div>
            `).join('');
                lucide.createIcons();

                const total = cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);
                document.getElementById('totalAmount').textContent = formatselling_price(total);

            }

            function updateQuantity(productId, newQuantity) {
                if (newQuantity < 1) return;

                cart = cart.map(item => item.id === productId ? {
                    ...item,
                    quantity: parseInt(newQuantity)
                } : item);
                renderCart();
            }

            function removeFromCart(productId) {
                cart = cart.filter(item => item.id !== productId);
                renderCart();
            }

            function formatCurrency(input) {
                const value = input.value.replace(/\D/g, '');
                input.value = new Intl.NumberFormat('id-ID').format(value);
                updateChange();
            }

            function updateChange() {
                const paymentAmountText = document.getElementById('paymentAmount').value.replace(/\D/g, '');
                const paymentAmount = Number(paymentAmountText);
                const totalAmountText = document.getElementById('totalAmount').textContent.replace('Rp ', '').replace(/\D/g,
                '');
                const totalAmount = Number(totalAmountText);

                if (paymentAmount >= totalAmount) {
                    const change = paymentAmount - totalAmount;
                    document.getElementById('changeAmount').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                } else {
                    document.getElementById('changeAmount').value = 'Rp 0';
                }
            }

            // Process Payment Button click

            function processTransaction() {

                // Periksa apakah keranjang kosong
                if (cart.length === 0) {
                    Swal.fire('Error', 'Keranjang kosong', 'error');
                    return;
                }

                // Hitung total harga
                const totalAmount = cart.reduce((sum, item) => sum + (item.selling_price * item.quantity), 0);

                // Ambil dan sanitasi input jumlah pembayaran
                const paymentAmountInput = document.getElementById('paymentAmount').value;
                const paymentAmount = parseInt(paymentAmountInput.replace(/\D/g, '')) || 0;
                const changeAmount = paymentAmount - totalAmount;

                // Validasi jumlah pembayaran
                if (paymentAmount < totalAmount) {
                    Swal.fire('Error', 'Jumlah pembayaran tidak cukup', 'error');
                    return;
                }
                if (paymentAmount <= 0) {
                    Swal.fire('Error', 'Jumlah pembayaran tidak valid', 'error');
                    return;
                }

                // Tampilkan SweetAlert konfirmasi
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Total pembayaran: ${totalAmount}, Jumlah yang dibayar: ${paymentAmount}, Kembalian: ${changeAmount}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Siapkan data transaksi
                        const transactionData = {
                            cart: cart.map(item => ({
                                id: item.id,
                                quantity: item.quantity,
                                price: item.selling_price,
                                name: item.name,
                                harga_modal: item.purchase_price,
                                nama_produk: item.name
                            })),
                            total: totalAmount,
                            payment: paymentAmount,
                            change: changeAmount
                        };

                        console.log(transactionData);

                        // Kirim data ke server
                        fetch('/transaksi/save', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(transactionData)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    printReceipt(data.receipt);
                                    clearAllItems();
                                    document.getElementById('paymentAmount').value = '';
                                    document.getElementById('changeAmount').value = '';
                                    Swal.fire({
                                        title: `Kembalian: Rp. ${changeAmount}`,
                                        text: 'Transaksi Berhasil',
                                        icon: 'success',
                                        confirmButtonText: 'Tutup'
                                    });
                                } else {
                                    Swal.fire('Error', data.message || 'Gagal memproses transaksi', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error', 'Gagal memproses transaksi', 'error');
                            });
                    } else {
                        // Jika user membatalkan, tidak melakukan transaksi
                        Swal.fire('Dibatalkan', 'Transaksi dibatalkan', 'info');
                    }
                });
            }

            function printReceipt(receipt) {
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Receipt</title>
                <style>
                    body {
                        font-family: 'Courier New', monospace;
                        font-size: 10px; /* Reduce font size for thermal paper */
                        width: 300px; /* Set a fixed width to match thermal paper size */
                        margin: 0;
                        padding: 0;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 10px;
                    }
                    .header h2 {
                        margin: 0;
                        font-size: 14px; /* Slightly larger title */
                    }
                    .header p {
                        margin: 5px 0;
                        font-size: 10px;
                    }
                    .item {
                        font-size: 10px;
                        margin: 5px 0;
                    }
                    .total {
                        margin-top: 10px;
                        border-top: 1px dashed #000;
                        padding-top: 5px;
                        font-size: 10px;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 10px;
                        font-size: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>STORE NAME</h2>
                    <p>Date: ${receipt.date}</p>
                </div>
                <div class="items">
                    ${receipt.items.map(item => `
                                <div class="item">
                                    ${item.name} x ${item.quantity}<br>
                                    ${formatselling_price(item.price)} = ${formatselling_price(item.price * item.quantity)}
                                </div>
                            `).join('')}
                </div>
                <div class="total">
                    <p>Total: ${formatselling_price(receipt.total)}</p>
                    <p>Payment: ${formatselling_price(receipt.payment)}</p>
                    <p>Change: ${formatselling_price(receipt.change)}</p>
                </div>
                <div class="footer">
                    <p>Thank you for your purchase!</p>
                </div>
            </body>
            </html>
        `);

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
        </script>


</body>

</html>
