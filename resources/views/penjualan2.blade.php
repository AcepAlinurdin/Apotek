<x-app-layout>
    {{-- Slot untuk judul halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Penjualan') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Kolom Kiri: Daftar Obat --}}
                <div class="bg-white p-4 rounded-xl shadow">
                    <h2 class="text-lg font-bold mb-2">Daftar Obat</h2>
                    <input type="text" id="search-medicine" placeholder="Cari obat..." class="w-full p-2 border rounded mb-3" />
                    <div class="overflow-y-auto max-h-96">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-gray-200 z-10">
                                <tr>
                                    <th class="p-2 text-left">Nama Obat</th>
                                    <th class="p-2">Harga</th>
                                    <th class="p-2">Stok</th>
                                    <th class="p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="medicine-list">
                                @foreach($obats as $obat)
                                <tr class="medicine-row" data-name="{{ $obat->nama_obat }}">
                                    <td class="p-2 text-left">{{ $obat->nama_obat }}</td>
                                    <td class="p-2 text-center">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="p-2 text-center">{{ $obat->stok }}</td>
                                    <td class="p-2 text-center">
                                        <button
                                            class="add-btn bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm disabled:bg-gray-400"
                                            data-name="{{ $obat->nama_obat }}" data-price="{{ $obat->harga_satuan }}"
                                            {{ $obat->stok <= 0 ? 'disabled' : '' }}>
                                            {{ $obat->stok <= 0 ? 'Habis' : 'Tambah' }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Keranjang Belanja --}}
                <div class="bg-white p-4 rounded-xl shadow">
                    <h2 class="text-lg font-bold mb-2">Keranjang Belanja</h2>
                    <div class="overflow-y-auto max-h-80">
                        <table class="w-full border-collapse text-center">
                            <thead class="sticky top-0 bg-gray-200 z-10">
                                <tr>
                                    <th class="p-2 text-left">Nama Obat</th>
                                    <th class="p-2">Jumlah</th>
                                    <th class="p-2 text-right">Total</th>
                                    <th class="p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                {{-- Item keranjang akan ditambahkan oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right text-xl font-bold">
                        Total: <span id="total-price">Rp 0</span>
                    </div>
                    <button id="checkout-btn" class="mt-4 w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 font-semibold">Checkout</button>
                </div>
            </div>

            {{-- Riwayat Penjualan di bawah --}}
            <div class="bg-white p-4 rounded-xl shadow mt-6">
                <h2 class="text-lg font-bold mb-2">Riwayat Penjualan</h2>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full border-collapse text-center">
                        <thead class="bg-gray-200 z-10">
                            <tr>
                                <th class="p-2 text-center">Tanggal</th>
                                <th class="p-2 text-left">Nama Obat</th>
                                <th class="p-2 text-center">Jumlah</th>
                                <th class="p-2 text-right">Subtotal</th>
                                <th class="p-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sales-history">
                            @forelse($riwayatPenjualans as $detail)
                            <tr>
                                <td class="p-2">{{ \Carbon\Carbon::parse($detail->penjualan->tanggal_penjualan)->format('d-m-Y H:i') }}</td>
                                <td class="p-2 text-left">{{ $detail->obat->nama_obat ?? 'Obat Dihapus' }}</td>
                                <td class="p-2">{{ $detail->jumlah }}</td>
                                <td class="p-2 text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="p-2 text-center">
                                    <button class="detail-btn bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs"
                                        data-id-transaksi="{{ $detail->penjualan->id }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($detail->penjualan->tanggal_penjualan)->isoFormat('dddd, D MMMM YYYY - HH:mm') }}"
                                        data-pegawai="{{ $detail->penjualan->user->name ?? 'Data Pengguna Tidak Ditemukan' }}"
                                        data-obat="{{ $detail->obat->nama_obat ?? 'Obat Dihapus' }}"
                                        data-jumlah="{{ $detail->jumlah }}"
                                        data-harga-satuan="{{ number_format($detail->harga_satuan, 0, ',', '.') }}"
                                        data-subtotal="{{ number_format($detail->subtotal, 0, ',', '.') }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-2 text-center text-gray-500">Belum ada riwayat penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@livewireScripts
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';
            
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
                        <td class="p-2 text-left">${medicineName}</td>
                        <td class="p-2">1</td>
                        <td class="p-2 text-right">Rp ${medicinePrice.toLocaleString('id-ID')}</td>
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

            document.querySelectorAll('.add-btn').forEach(button => {
                button.addEventListener('click', function () {
                    addToCart(this);
                });
            });

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
            
            document.getElementById('search-medicine').addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                document.querySelectorAll('.medicine-row').forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    row.style.display = name.includes(filter) ? '' : 'none';
                });
            });

            document.getElementById('sales-history').addEventListener('click', function(event) {
                if (event.target.classList.contains('detail-btn')) {
                    const button = event.target;
                    const data = button.dataset;

                    const detailHtml = `
                        <div class="text-left text-sm">
                            <p class="mb-2"><strong>ID Transaksi:</strong> ${data.idTransaksi}</p>
                            <p class="mb-2"><strong>Tanggal:</strong> ${data.tanggal}</p>
                            <p class="mb-4"><strong>Dilayani oleh:</strong> ${data.pegawai}</p>
                            <hr class="my-3">
                            <p class="mb-2"><strong>Nama Obat:</strong> ${data.obat}</p>
                            <p class="mb-2"><strong>Harga Satuan:</strong> Rp ${data.hargaSatuan}</p>
                            <p class="mb-2"><strong>Jumlah:</strong> ${data.jumlah} unit</p>
                            <hr class="my-3">
                            <p class="text-lg"><strong>Subtotal:</strong> Rp ${data.subtotal}</p>
                        </div>
                    `;

                    Swal.fire({
                        title: '<strong>Detail Transaksi</strong>',
                        icon: 'info',
                        html: detailHtml,
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonText: 'Tutup'
                    });
                }
            });
        });
    </script>
    @endpush
     @stack('scripts') 
</x-app-layout>
