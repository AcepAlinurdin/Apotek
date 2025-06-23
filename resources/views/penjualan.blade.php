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
<body class="bg-gray-100 p-8">
  <nav class="bg-green-700 text-white p-4 rounded-xl mb-4 flex justify-between items-center">
  <h1 class="text-xl font-bold">Apotek Parakan Muncang</h1>
  <div class="space-x-4">
    <a href="/master_data" class="hover:underline">Master Data</a>
    <a href="/perhitungan" class="hover:underline">Pembelian</a>
    <a href="/penjualan" class="font-bold underline">Transaksi</a>
    <a href="/cek" class="hover:underline">Pengecekan stok</a>
    <a href="/karyawan" class="hover:underline">Karyawan</a>
  </div>
</nav>


    <div class="grid grid-cols-2 gap-6">
        {{-- Daftar Obat --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>
            <input type="text" id="search-medicine" placeholder="Cari obat..." class="w-full p-2 border rounded mb-3" />
            {{-- Wrapper untuk scroll --}}
            <div class="overflow-y-auto max-h-80">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 bg-gray-200 z-10"> {{-- Header dibuat sticky --}}
                        <tr>
                            <th class="p-2">Nama Obat</th>
                            <th class="p-2">Harga</th>
                            <th class="p-2">Stok</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="medicine-list">
                        @foreach($obats as $obat)
                        <tr class="medicine-row " data-name="{{ $obat->nama_obat }}">
                            <td class="p-2 text-start">{{ $obat->nama_obat }}</td>
                            <td class="p-2 text-center">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                            {{-- PERBAIKAN: Menampilkan total_stok hasil agregasi --}}
                            <td class="p-2 text-center">{{ $obat->total_stok }}</td> 
                            <td class="p-2 text-center">
                                <button
                                    class="add-btn bg-green-500 text-white px-3 py-1 rounded hover:bg-green-300 text-sm"
                                    data-name="{{ $obat->nama_obat }}"
                                    data-price="{{ $obat->harga_satuan }}"
                                    {{ $obat->total_stok <= 0 ? 'disabled' : '' }}
                                >
                                    {{ $obat->total_stok <= 0 ? 'Stok Habis' : 'Tambahkan' }}
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Keranjang Belanja --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-2">Keranjang Belanja</h2>
            {{-- Wrapper untuk scroll --}}
            <div class="overflow-y-auto max-h-72">
                <table class="w-full border-collapse text-center">
                    <thead class="sticky top-0 bg-gray-200 z-10 "> {{-- Header dibuat sticky --}}
                        <tr>
                            <th class="p-2">Nama Obat</th>
                            <th class="p-2">Jumlah</th>
                            <th class="p-2">Total</th>
                            <th class="p-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-items"></tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <strong>Total: <span id="total-price">Rp 0</span></strong>
            </div>
            <button id="checkout-btn" class="mt-4 w-full bg-green-500 text-white p-2 rounded">Checkout</button>
        </div>
    </div>

    
<div class="bg-white p-4 rounded-xl shadow mt-6">
    <h2 class="text-lg font-bold mb-2">Riwayat Penjualan</h2>
    
    <table class="w-full border-collapse text-center">
        <thead class="sticky top-0 bg-gray-200 z-10"> 
            <tr>
                <th class="p-2 text-center">Tanggal</th>
                <th class="p-2">Nama Obat</th>
                <th class="p-2 text-center">Jumlah</th>
                <th class="p-2 text-center">Total Harga</th>
            </tr>
        </thead>
        <tbody id="sales-history">
            @if($riwayatPenjualans->count() > 0)
                @foreach($riwayatPenjualans as $penjualan)
                <tr>
                    <td class="p-2">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y') }}</td>
                    <td class="p-2">{{ $penjualan->nama_obat }}</td>
                    <td class="p-2">{{ $penjualan->qty }}</td>
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
    {{-- </div> --}} {{-- Penutup div wrapper scroll untuk Riwayat Penjualan --}}
</div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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
                    cartRow.dataset.price = medicinePrice;
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
                    name: item.dataset.name, 
                    quantity: parseInt(item.querySelector('td:nth-child(2)').textContent),
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
            .then((res) => { 
                if (!res.ok) { 
                    return res.json().then(errData => { throw errData; }); 
                }
                return res.json(); 
            })
            .then((data) => {
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Checkout Berhasil',
                        text: data.message,
                    });

                    document.getElementById('cart-items').innerHTML = '';
                    document.getElementById('total-price').textContent = 'Rp 0';

                    
                    location.reload();

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout Gagal',
                        text: data.message || 'Terjadi kesalahan yang tidak diketahui.', 
                    });
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
                let errorMessage = 'Terjadi kesalahan saat checkout. Silakan coba lagi.';
                if (error && error.message) { 
                    errorMessage = error.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: errorMessage,
                });
            });
        });

        
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
