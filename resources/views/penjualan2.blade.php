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
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Daftar Obat</h2>
                    <input type="text" id="search-medicine" placeholder="Cari obat..." class="w-full p-2 border border-gray-300 rounded-md mb-4 focus:ring-indigo-500 focus:border-indigo-500" />
                    <div class="overflow-y-auto max-h-96">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-gray-200 z-10">
                                <tr>
                                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Obat</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Harga</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Stok</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="medicine-list">
                                @foreach($obats as $obat)
                                <tr class="medicine-row border-b hover:bg-gray-50" data-name="{{ strtolower($obat->nama_obat) }}">
                                    <td class="p-3 text-left font-medium text-gray-700">{{ $obat->nama_obat }}</td>
                                    <td class="p-3 text-center text-gray-600">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="p-3 text-center font-bold {{ $obat->stok <= 10 ? 'text-red-500' : 'text-gray-700' }}">{{ $obat->stok }}</td>
                                    <td class="p-3 text-center">
                                        <button
                                            class="add-to-cart-btn bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            data-id="{{ $obat->id }}" 
                                            data-name="{{ $obat->nama_obat }}" 
                                            data-price="{{ $obat->harga_satuan }}"
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
                <div class="bg-white p-6 rounded-xl shadow-lg flex flex-col">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Keranjang Belanja</h2>
                    <div class="overflow-y-auto max-h-80 flex-grow">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-gray-200 z-10">
                                <tr>
                                    <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Obat</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Jumlah</th>
                                    <th class="p-3 text-right text-sm font-semibold text-gray-600">Total</th>
                                    <th class="p-3 text-sm font-semibold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t text-right text-xl font-bold text-gray-800">
                        Total: <span id="total-price">Rp 0</span>
                    </div>
                    <button id="checkout-btn" class="mt-4 w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 font-semibold text-lg transition-colors">Checkout</button>
                </div>
            </div>

            {{-- Riwayat Penjualan di bawah --}}
            <div class="bg-white p-6 rounded-xl shadow-lg mt-6">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Riwayat Penjualan Terakhir</h2>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full border-collapse">
                        <thead class="sticky top-0 bg-gray-200 z-10">
                            <tr>
                                <th class="p-3 text-center text-sm font-semibold text-gray-600">Tanggal</th>
                                <th class="p-3 text-left text-sm font-semibold text-gray-600">Nama Obat</th>
                                <th class="p-3 text-center text-sm font-semibold text-gray-600">Jumlah</th>
                                <th class="p-3 text-right text-sm font-semibold text-gray-600">Subtotal</th>
                                <th class="p-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="sales-history">
                            @forelse($riwayatPenjualans as $detail)
                            <tr>
                                <td class="p-3 text-sm text-center text-gray-600">{{ \Carbon\Carbon::parse($detail->penjualan->tanggal_penjualan)->format('d-m-Y') }}</td>
                                <td class="p-3 text-sm text-left text-gray-700">{{ $detail->obat->nama_obat ?? 'Obat Dihapus' }}</td>
                                <td class="p-3 text-sm text-center text-gray-600">{{ $detail->jumlah }}</td>
                                <td class="p-3 text-sm text-right text-gray-600">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="p-3 text-sm text-center">
                                    <button class="detail-btn bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs"
                                        data-id-transaksi="{{ $detail->penjualan->id }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($detail->penjualan->tanggal_penjualan)->isoFormat('dddd, D MMMM YYYY - HH:mm') }}"
                                        data-pegawai="{{ $detail->penjualan->user->name ?? 'N/A' }}"
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
                                <td colspan="5" class="p-4 text-center text-gray-500">Belum ada riwayat penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Setup CSRF token untuk semua request AJAX
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            let cart = {}; // Variabel untuk menyimpan keranjang belanja

            // Fungsi untuk mengupdate tampilan keranjang dan total harga
            const renderCart = () => {
                const cartContainer = $('#cart-items');
                cartContainer.empty();
                let totalPrice = 0;

                if (Object.keys(cart).length === 0) {
                    cartContainer.html('<tr><td colspan="4" class="p-4 text-center text-gray-400">Keranjang masih kosong</td></tr>');
                } else {
                    Object.values(cart).forEach(item => {
                        const subtotal = item.price * item.quantity;
                        totalPrice += subtotal;
                        const row = `
                            <tr class="border-b" data-id="${item.id}">
                                <td class="p-3 text-left text-sm text-gray-700">${item.name}</td>
                                <td class="p-3 text-center">
                                    <input type="number" class="cart-quantity w-16 text-center border rounded-md" value="${item.quantity}" min="1">
                                </td>
                                <td class="p-3 text-right text-sm text-gray-600">Rp ${subtotal.toLocaleString('id-ID')}</td>
                                <td class="p-3 text-center">
                                    <button class="remove-btn text-red-500 hover:text-red-700 font-bold">X</button>
                                </td>
                            </tr>
                        `;
                        cartContainer.append(row);
                    });
                }
                $('#total-price').text(`Rp ${totalPrice.toLocaleString('id-ID')}`);
            };

            // Event listener untuk tombol 'Tambah' di daftar obat
            $('#medicine-list').on('click', '.add-to-cart-btn', function () {
                const button = $(this);
                const obatId = button.data('id');
                const obatName = button.data('name');
                const obatPrice = button.data('price');

                if (cart[obatId]) {
                    cart[obatId].quantity++;
                } else {
                    cart[obatId] = { id: obatId, name: obatName, price: obatPrice, quantity: 1 };
                }
                renderCart();
            });

            // Event listener untuk mengubah jumlah barang di keranjang
            $('#cart-items').on('change', '.cart-quantity', function () {
                const input = $(this);
                const obatId = input.closest('tr').data('id');
                const newQuantity = parseInt(input.val());

                if (cart[obatId] && newQuantity > 0) {
                    cart[obatId].quantity = newQuantity;
                } else {
                    input.val(cart[obatId].quantity); // Kembalikan ke nilai semula jika tidak valid
                }
                renderCart();
            });

            // Event listener untuk tombol 'Hapus' di keranjang
            $('#cart-items').on('click', '.remove-btn', function () {
                const obatId = $(this).closest('tr').data('id');
                delete cart[obatId];
                renderCart();
            });

            // Event listener untuk tombol 'Checkout'
            $('#checkout-btn').on('click', function () {
                const cartItems = Object.values(cart);
                if (cartItems.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Tambahkan obat terlebih dahulu.' });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Transaksi',
                    text: `Total belanja Anda adalah ${$('#total-price').text()}. Lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('checkout') }}",
                            method: 'POST',
                            data: {
                                cartItems: cartItems.map(item => ({ id: item.id, quantity: item.quantity }))
                            },
                            success: (response) => {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message })
                                .then(() => window.location.reload());
                            },
                            error: (xhr) => {
                                const error = xhr.responseJSON;
                                Swal.fire({ icon: 'error', title: 'Gagal!', text: error.message || 'Terjadi kesalahan.' });
                            }
                        });
                    }
                });
            });
            
            // Event listener untuk fitur pencarian obat
            $('#search-medicine').on('keyup', function () {
                const filter = $(this).val().toLowerCase();
                $('.medicine-row').each(function () {
                    const name = $(this).data('name');
                    $(this).toggle(name.includes(filter));
                });
            });

            // Event listener untuk tombol detail di riwayat penjualan
            $('#sales-history').on('click', '.detail-btn', function() {
                const data = $(this).data();
                const detailHtml = `
                    <div class="text-left text-sm">
                        <p class="mb-2"><strong>ID Transaksi:</strong> ${data.idTransaksi}</p>
                        <p class="mb-2"><strong>Tanggal:</strong> ${data.tanggal}</p>
                        <p class="mb-4"><strong>Dilayani oleh:</strong> ${data.pegawai}</p>
                        <hr class="my-3"><p class="mb-2"><strong>Nama Obat:</strong> ${data.obat}</p>
                        <p class="mb-2"><strong>Harga Satuan:</strong> Rp ${data.hargaSatuan}</p>
                        <p class="mb-2"><strong>Jumlah:</strong> ${data.jumlah} unit</p>
                        <hr class="my-3"><p class="text-lg"><strong>Subtotal:</strong> Rp ${data.subtotal}</p>
                    </div>`;
                
                Swal.fire({
                    title: '<strong>Detail Transaksi</strong>',
                    icon: 'info',
                    html: detailHtml,
                    showCloseButton: true,
                    confirmButtonText: 'Tutup'
                });
            });

            // Panggil renderCart() saat halaman pertama kali dimuat
            renderCart();
        });
    </script>
    @endpush
</x-app-layout>