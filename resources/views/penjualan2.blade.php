<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($pakets) && $pakets->isNotEmpty())
            <div class="bg-white p-4 rounded-xl shadow-lg mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold text-gray-800">Paket Obat Cepat</h3>
                    <a href="{{ route('paket-obat.index') }}"
                        class="flex items-center px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </a>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($pakets as $paket)
                    <button
                        class="paket-btn px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition text-sm font-semibold"
                        data-id="{{ $paket->id }}" data-name="{{ $paket->nama_paket }}">
                        {{ $paket->nama_paket }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Kolom Kiri: Daftar Obat --}}
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Daftar Obat</h2>
                    <input type="text" id="search-medicine" placeholder="Cari obat..."
                        class="w-full p-2 border border-gray-300 rounded-md mb-4 focus:ring-indigo-500 focus:border-indigo-500" />
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
                                <tr class="medicine-row border-b hover:bg-gray-50"
                                    data-name="{{ strtolower($obat->nama_obat) }}">
                                    <td class="p-3 text-left font-medium text-gray-700">{{ $obat->nama_obat }}</td>
                                    <td class="p-3 text-center text-gray-600">Rp
                                        {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                                    <td
                                        class="p-3 text-center font-bold {{ $obat->stok <= 10 ? 'text-red-500' : 'text-gray-700' }}">
                                        {{ $obat->stok }}</td>
                                    <td class="p-3 text-center">
                                        <button
                                            class="add-to-cart-btn bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 text-sm disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            data-id="{{ $obat->id }}" data-name="{{ $obat->nama_obat }}"
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

                    {{-- ====================================================================== --}}
                    {{-- =================== BLOK PASIEN: KHUSUS APOTEKER ===================== --}}
                    {{-- ====================================================================== --}}

                    <div id="patient-section" class="mb-4 p-3 bg-gray-50 rounded-lg border">
                        <div class="flex justify-between items-center mb-2">
                            <label for="search-patient" class="block text-sm font-medium text-gray-700">Pasien
                                (Opsional)</label>
                        </div>

                        <div id="patient-search-container" class="relative">
                            <input type="text" id="search-patient" placeholder="Cari nama atau No. HP pasien..."
                                class="w-full p-2 border border-gray-300 rounded-md">
                            <div id="patient-suggestions"
                                class="absolute z-20 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-48 overflow-y-auto shadow-lg">
                            </div>
                        </div>

                        <div id="selected-patient-info" class="hidden mt-2 p-3 bg-indigo-100 rounded-md">
                            {{-- Info pasien akan ditampilkan di sini oleh JavaScript --}}
                        </div>
                        <input type="hidden" id="selected-patient-id" name="patient_id">
                    </div>

                    {{-- ====================================================================== --}}
                    {{-- ======================= AKHIR BLOK PASIEN ============================ --}}
                    {{-- ====================================================================== --}}

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
                                {{-- Diisi oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 pt-4 border-t text-right text-xl font-bold text-gray-800">
                        Total: <span id="total-price">Rp 0</span>
                    </div>
                    <button id="checkout-btn"
                        class="mt-4 w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 font-semibold text-lg transition-colors">Checkout</button>
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
                                <td class="p-3 text-sm text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($detail->penjualan->tanggal_penjualan)->format('d-m-Y') }}
                                </td>
                                <td class="p-3 text-sm text-left text-gray-700">
                                    {{ $detail->obat->nama_obat ?? 'Obat Dihapus' }}</td>
                                <td class="p-3 text-sm text-center text-gray-600">{{ $detail->jumlah }}</td>
                                <td class="p-3 text-sm text-right text-gray-600">Rp
                                    {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="p-3 text-sm text-center">
                                    <button
                                        class="detail-btn bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs"
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let cart = {};
            const renderCart = () => {
                const cartContainer = $('#cart-items');
                cartContainer.empty();
                let totalPrice = 0;

                if (Object.keys(cart).length === 0) {
                    cartContainer.html(
                        '<tr><td colspan="4" class="p-4 text-center text-gray-400">Keranjang masih kosong</td></tr>'
                        );
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

            $('#medicine-list').on('click', '.add-to-cart-btn', function () {
                const button = $(this);
                const obatId = button.data('id');
                const obatName = button.data('name');
                const obatPrice = button.data('price');

                if (cart[obatId]) {
                    cart[obatId].quantity++;
                } else {
                    cart[obatId] = {
                        id: obatId,
                        name: obatName,
                        price: obatPrice,
                        quantity: 1
                    };
                }
                renderCart();
            });

            $('#cart-items').on('change', '.cart-quantity', function () {
                const input = $(this);
                const obatId = input.closest('tr').data('id');
                const newQuantity = parseInt(input.val());

                if (cart[obatId] && newQuantity > 0) {
                    cart[obatId].quantity = newQuantity;
                } else if (cart[obatId]) {
                    input.val(cart[obatId].quantity); // Reset to previous value if invalid
                }
                renderCart();
            });

            $('#cart-items').on('click', '.remove-btn', function () {
                const obatId = $(this).closest('tr').data('id');
                delete cart[obatId];
                renderCart();
            });

            // --- LOGIKA PENCARIAN & PEMILIHAN PASIEN ---

            let searchTimeout;
            $('#search-patient').on('keyup', function () {
                const query = $(this).val();
                const suggestionsContainer = $('#patient-suggestions');
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    suggestionsContainer.hide().empty();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    $.ajax({
                        url: "{{ route('api.pasien.search') }}", // Pastikan route ini ada
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function (response) {
                            suggestionsContainer.empty().show();
                            if (response.length > 0) {
                                response.forEach(pasien => {
                                    const suggestionItem = `<div class="p-2 hover:bg-gray-100 cursor-pointer patient-suggestion-item" data-id="${pasien.id}" data-name="${pasien.nama_lengkap}">
                                        <p class="font-semibold">${pasien.nama_lengkap}</p>
                                        <p class="text-xs text-gray-500">${pasien.nomor_telepon || ''}</p>
                                    </div>`;
                                    suggestionsContainer.append(
                                        suggestionItem);
                                });
                            } else {
                                suggestionsContainer.html(
                                    '<div class="p-2 text-center text-gray-500">Pasien tidak ditemukan.</div>'
                                    );
                            }
                        }
                    });
                }, 400); // Debounce 400ms
            });

            $(document).on('click', '.patient-suggestion-item', function () {
                const patientId = $(this).data('id');
                const patientName = $(this).data('name');

                $('#selected-patient-id').val(patientId);
                $('#patient-search-container').hide();
                $('#patient-suggestions').hide().empty();

                const selectedInfoHtml = `
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-600">Pasien Terpilih:</p>
                            <p class="font-bold text-indigo-800">${patientName}</p>
                        </div>
                        <div>
                            <button id="view-patient-history" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Riwayat</button>
                            <button id="change-patient" class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 ml-1">Ganti</button>
                        </div>
                    </div>`;
                $('#selected-patient-info').html(selectedInfoHtml).show();
            });

            $(document).on('click', '#change-patient', function () {
                $('#selected-patient-id').val('');
                $('#search-patient').val('');
                $('#selected-patient-info').hide().empty();
                $('#patient-search-container').show();
            });

            $(document).on('click', '#view-patient-history', function () {
                const patientId = $('#selected-patient-id').val();
                if (!patientId) return;

                $.ajax({
                    url: `/api/pasien/${patientId}/riwayat`, // Pastikan route ini ada
                    method: 'GET',
                    success: function (response) {
                        let historyHtml =
                            '<div class="text-left text-sm max-h-80 overflow-y-auto">';
                        if (response.riwayat && response.riwayat.length > 0) {
                            historyHtml += response.riwayat.map(item => `
                                <div class="p-2 border-b last:border-b-0">
                                    <p class="font-semibold">${item.obat ? item.obat.nama_obat : 'Obat Dihapus'}</p>
                                    <p class="text-xs text-gray-600">Tgl: ${new Date(item.penjualan.tanggal_penjualan).toLocaleDateString('id-ID')} | Jumlah: ${item.jumlah}</p>
                                </div>
                            `).join('');
                        } else {
                            historyHtml +=
                                '<p class="text-center text-gray-500 py-4">Tidak ada riwayat pembelian.</p>';
                        }
                        historyHtml += '</div>';

                        Swal.fire({
                            title: `<strong>Riwayat: ${response.pasien.nama_lengkap}</strong>`,
                            icon: 'info',
                            html: historyHtml,
                            showCloseButton: true,
                            confirmButtonText: 'Tutup'
                        });
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal memuat riwayat pasien.', 'error');
                    }
                });
            });



            $('.paket-btn').on('click', function () {
                const paketId = $(this).data('id');
                const paketName = $(this).data('name');

                // Tampilkan loading
                Swal.fire({
                    title: `Menambahkan ${paketName}...`,
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false
                });

                $.ajax({
                    url: `/api/paket-obat/${paketId}`, // Kita akan membuat route ini
                    method: 'GET',
                    success: function (response) {
                        if (response.length > 0) {
                            response.forEach(detail => {
                                const obat = detail.obat;
                                if (!obat) return; // Skip jika obat tidak ditemukan

                                if (cart[obat.id]) {
                                    // Jika obat sudah ada di keranjang, tambahkan jumlahnya
                                    cart[obat.id].quantity += detail.jumlah;
                                } else {
                                    // Jika belum ada, tambahkan sebagai item baru
                                    cart[obat.id] = {
                                        id: obat.id,
                                        name: obat.nama_obat,
                                        price: obat.harga_satuan,
                                        quantity: detail.jumlah
                                    };
                                }
                            });
                            renderCart(); // Perbarui tampilan keranjang
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `${paketName} telah ditambahkan ke keranjang.`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Info', 'Paket ini tidak berisi obat apapun.',
                            'info');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal mengambil data paket. Silakan coba lagi.',
                            'error');
                    }
                });
            });
            // --- FUNGSI CHECKOUT ---



            $('#checkout-btn').on('click', function () {
                const cartItems = Object.values(cart);
                if (cartItems.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keranjang Kosong',
                        text: 'Tambahkan obat terlebih dahulu.'
                    });
                    return;
                }

                // Ambil ID Pasien (jika ada)
                const patientId = $('#selected-patient-id').val();

                Swal.fire({
                    title: 'Konfirmasi Transaksi',
                    text: `Total belanja Anda adalah ${$('#total-price').text()}. Lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('checkout') }}",
                            method: 'POST',
                            data: {
                                // Kirim ID Pasien ke backend
                                patient_id: patientId,
                                cartItems: cartItems.map(item => ({
                                    id: item.id,
                                    quantity: item.quantity
                                }))
                            },
                            success: (response) => {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message
                                    })
                                    .then(() => window.location.reload());
                            },
                            error: (xhr) => {
                                const error = xhr.responseJSON;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: error.message ||
                                        'Terjadi kesalahan.'
                                });
                            }
                        });
                    }
                });
            });

            // --- FUNGSI LAINNYA ---

            $('#search-medicine').on('keyup', function () {
                const filter = $(this).val().toLowerCase();
                $('.medicine-row').each(function () {
                    const name = $(this).data('name');
                    $(this).toggle(name.includes(filter));
                });
            });

            $('#sales-history').on('click', '.detail-btn', function () {
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

            renderCart();
        });

    </script>
    @endpush
</x-app-layout>
