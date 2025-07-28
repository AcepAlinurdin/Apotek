<x-app-layout>
    {{-- Slot untuk judul halaman yang akan muncul di header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Penjualan') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman Anda dimulai di sini --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Wrapper untuk konten agar sesuai dengan layout --}}
            <div class="bg-gray-100">

                {{-- Mulai dari sini adalah seluruh konten yang sudah Anda buat --}}
                <div class="grid grid-cols-2 gap-6">
                    {{-- Daftar Obat --}}
                    <div class="bg-white p-4 rounded-xl shadow">
                        <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>
                        <input type="text" id="search-medicine" placeholder="Cari obat..."
                            class="w-full p-2 border rounded mb-3" />
                        <div class="overflow-y-auto max-h-80">
                            <table class="w-full border-collapse">
                                <thead class="sticky top-0 bg-gray-200 z-10">
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
                                        <td class="p-2 text-center">{{ $obat->total_stok }}</td>
                                        <td class="p-2 text-center">
                                            <button
                                                class="add-btn bg-green-500 text-white px-3 py-1 rounded hover:bg-green-300 text-sm"
                                                data-name="{{ $obat->nama_obat }}" data-price="{{ $obat->harga_satuan }}"
                                                {{ $obat->total_stok <= 0 ? 'disabled' : '' }}>
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
                        <div class="overflow-y-auto max-h-72">
                            <table class="w-full border-collapse text-center">
                                <thead class="sticky top-0 bg-gray-200 z-10 ">
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
                        <button id="checkout-btn"
                            class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Checkout</button>
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
                            @forelse($riwayatPenjualans as $penjualan)
                            <tr>
                                <td class="p-2">{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y') }}</td>
                                <td class="p-2">{{ $penjualan->nama_obat }}</td>
                                <td class="p-2">{{ $penjualan->qty }}</td>
                                <td class="p-2">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-2 text-center text-gray-500">Belum ada riwayat penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div> {{-- Akhir dari wrapper konten --}}
        </div>
    </div>

    @push('scripts')
    {{-- Memasukkan script Anda ke dalam stack 'scripts' milik layout --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Fungsi untuk mengupdate total harga
            function updateTotalPrice() {
                let total = 0;
                document.querySelectorAll('#cart-items tr').forEach(row => {
                    const priceText = row.querySelector('td:nth-child(3)').textContent.replace('Rp ', '').replace(/\./g, '');
                    total += parseInt(priceText) || 0;
                });
                document.getElementById('total-price').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            }

            function addToCart(button) {
                const medicineName = button.dataset.name;
                const medicinePrice = parseInt(button.dataset.price);
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
            }

            // Event listener untuk semua tombol 'Tambahkan'
            document.querySelectorAll('.add-btn').forEach(button => {
                button.addEventListener('click', function () {
                    addToCart(this);
                });
            });

            // Event listener untuk tombol checkout
            document.getElementById('checkout-btn').addEventListener('click', function () {
                const cartItemsEl = document.querySelectorAll('#cart-items tr');
                if (cartItemsEl.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Tambahkan obat terlebih dahulu' });
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
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    if (status === 200 && body.success) {
                        Swal.fire({ icon: 'success', title: 'Checkout Berhasil', text: body.message })
                        .then(() => location.reload());
                    } else {
                        throw body;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.fire({ icon: 'error', title: 'Kesalahan', text: error.message || 'Terjadi kesalahan saat checkout.' });
                });
            });
            
            // Event listener untuk search
            document.getElementById('search-medicine').addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                document.querySelectorAll('.medicine-row').forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    row.style.display = name.includes(filter) ? '' : 'none';
                });
            });
        });
    </script>
    @endpush

</x-app-layout>