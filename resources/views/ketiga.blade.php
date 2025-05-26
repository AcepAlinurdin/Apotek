<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Penjualan Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 p-6">
    <nav class="bg-green-700 text-white p-4 rounded-xl mb-4">
        <h1 class="text-xl font-bold">Penjualan Obat</h1>
    </nav>

    <div class="grid grid-cols-2 gap-6">
        {{-- Daftar Obat --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>
            <input type="text" id="search-medicine" placeholder="Cari obat..." class="w-full p-2 border rounded mb-3" />
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Nama Obat</th>
                        <th class="p-2">Harga</th>
                        <th class="p-2">Stok</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="medicine-list">
                    @foreach($obats as $obat)
                    <tr class="medicine-row" data-name="{{ $obat->nama_obat }}">
                        <td class="p-2">{{ $obat->nama_obat }}</td>
                        <td class="p-2">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                        <td class="p-2">{{ $obat->qty }}</td>
                        <td class="p-2">
                            <button
                                class="add-btn bg-blue-500 text-white px-3 py-1 rounded"
                                data-name="{{ $obat->nama_obat }}"
                                data-price="{{ $obat->harga_satuan }}"
                                {{ $obat->qty <= 0 ? 'disabled' : '' }}
                            >
                                {{ $obat->qty <= 0 ? 'Stok Habis' : 'Tambah' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Keranjang Belanja --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Keranjang Belanja</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Nama Obat</th>
                        <th class="p-2">Jumlah</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-items"></tbody>
            </table>
            <div class="mt-4 text-right">
                <strong>Total: <span id="total-price">Rp 0</span></strong>
            </div>
            <button id="checkout-btn" class="mt-4 w-full bg-green-500 text-white p-2 rounded">Checkout</button>
        </div>
    </div>

    {{-- Riwayat Penjualan --}}
    <div class="bg-white p-4 rounded-xl shadow mt-6">
        <h2 class="text-lg font-bold mb-2">Riwayat Penjualan</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Tanggal</th>
                    <th class="p-2">Nama Obat</th>
                    <th class="p-2">Jumlah</th>
                    <th class="p-2">Total Harga</th> {{-- Mengganti "Total" menjadi "Total Harga" agar lebih jelas --}}
                </tr>
            </thead>
            <tbody id="sales-history">
                {{-- Cek apakah ada riwayat penjualan --}}
                @if($riwayatPenjualans->count() > 0)
                    @foreach($riwayatPenjualans as $penjualan)
                    <tr>
                        {{-- Format tanggal. Asumsi 'tanggal' adalah objek Carbon atau string tanggal Y-m-d H:i:s --}}
                        <td class="p-2">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y H:i') }}</td>
                        <td class="p-2">{{ $penjualan->nama_obat }}</td>
                        <td class="p-2">{{ $penjualan->qty }}</td>
                        {{-- Format total_harga sebagai mata uang Rupiah --}}
                        <td class="p-2">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="p-2 text-center text-gray-500">Belum ada riwayat penjualan.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // ... (kode JavaScript untuk tambah obat ke keranjang dan updateTotalPrice tetap sama) ...
        document.querySelectorAll('.add-btn').forEach(button => {
            button.addEventListener('click', function () {
                const medicineName = this.dataset.name;
                const medicinePrice = parseInt(this.dataset.price);
                const existingItem = document.querySelector(`#cart-items tr[data-name="${medicineName}"]`);

                if (existingItem) {
                    const qtyCell = existingItem.querySelector('td:nth-child(2)');
                    const currentQty = parseInt(qtyCell.textContent);
                    qtyCell.textContent = currentQty + 1;
                    const totalCell = existingItem.querySelector('td:nth-child(3)');
                    totalCell.textContent = `Rp ${(medicinePrice * (currentQty + 1)).toLocaleString('id-ID')}`;
                } else {
                    const cartRow = document.createElement('tr');
                    cartRow.dataset.name = medicineName;
                    cartRow.dataset.price = medicinePrice; // Tetap simpan harga satuan di data-attribute untuk JS keranjang
                    cartRow.innerHTML = `
                        <td class="p-2">${medicineName}</td>
                        <td class="p-2">1</td>
                        <td class="p-2">Rp ${medicinePrice.toLocaleString('id-ID')}</td>
                        <td class="p-2">
                            <button class="remove-btn bg-red-500 text-white px-2 py-1 rounded text-sm">Hapus</button>
                        </td>
                    `;
                    document.getElementById('cart-items').appendChild(cartRow);
                    cartRow.querySelector('.remove-btn').addEventListener('click', function () {
                        cartRow.remove();
                        updateTotalPrice();
                    });
                }
                updateTotalPrice();
            });
        });

        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('#cart-items tr').forEach(row => {
                const priceText = row.querySelector('td:nth-child(3)').textContent.replace('Rp ', '').replace(/\./g, '');
                total += parseInt(priceText);
            });
            document.getElementById('total-price').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }


        // Checkout dan kirim data ke server
        document.getElementById('checkout-btn').addEventListener('click', function () {
            const cartItemsEl = document.querySelectorAll('#cart-items tr');

            if (cartItemsEl.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang Kosong',
                    text: 'Tambahkan obat terlebih dahulu sebelum checkout',
                });
                return;
            }

            let cartItems = [];
            cartItemsEl.forEach(item => {
                cartItems.push({
                    name: item.dataset.name, // Nama obat dari data-attribute
                    quantity: parseInt(item.querySelector('td:nth-child(2)').textContent),
                    // Harga satuan tidak perlu dikirim ke backend untuk disimpan di data_penjualan
                    // karena backend akan mengambilnya dari DataObat berdasarkan 'name'
                });
            });

            fetch('{{ route("checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ cartItems }),
            })
            .then((res) => { // Periksa apakah respons adalah JSON sebelum parsing
                if (!res.ok) { // Jika status bukan 2xx (misalnya 400, 500)
                    return res.json().then(errData => { throw errData; }); // Coba parse error JSON
                }
                return res.json(); // Jika OK, parse JSON sukses
            })
            .then((data) => {
                // 'data.message' berisi pesan dari controller
                // 'data.success' adalah boolean dari controller
                if (data.success) { // Cek flag 'success' dari respons JSON
                    Swal.fire({
                        icon: 'success',
                        title: 'Checkout Berhasil',
                        text: data.message,
                    });

                    // Kosongkan keranjang belanja di UI
                    document.getElementById('cart-items').innerHTML = '';
                    document.getElementById('total-price').textContent = 'Rp 0';

                    // Reload halaman untuk memperbarui stok obat dan daftar riwayat penjualan dari server
                    location.reload();

                } else { // Jika data.success adalah false
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout Gagal',
                        text: data.message || 'Terjadi kesalahan yang tidak diketahui.', // Fallback message
                    });
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
                let errorMessage = 'Terjadi kesalahan saat checkout. Silakan coba lagi.';
                if (error && error.message) { // Jika error adalah objek dengan properti message (dari throw errData atau network error)
                    errorMessage = error.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: errorMessage,
                });
            });
        });

        // Filter pencarian obat
        document.getElementById('search-medicine').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.medicine-row').forEach(row => {
                const name = row.dataset.name.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>