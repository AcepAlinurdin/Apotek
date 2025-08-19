    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pengelolaan Data Obat') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <div id="notification" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline" id="notification-message"></span>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold text-gray-700">Formulir Data Obat</h2>

    {{-- [MODIFIKASI] Wrapper untuk menampung kedua tombol --}}
    <div class="flex space-x-2">
        {{-- [BARU] Tombol untuk Kelola Pengguna --}}
        @hasanyrole('admin|kepala apotek')
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Kelola Pengguna
            </a>
        @endhasanyrole

        {{-- Tombol Kelola Supplier yang sudah ada --}}
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600">
            Kelola Supplier
        </a>
    </div>
</div>
                        <form id="formObat">
                            <input type="hidden" id="obat_id" name="obat_id">
                            <div class="grid md:grid-cols-4 grid-cols-2 gap-4">
                                <div class="md:col-span-1">
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                                <input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <div class="text-red-500 text-xs mt-1 error-text error-tanggal"></div>
                            </div>
                                <div class="md:col-span-2">
                                    <label for="nama_obat" class="block text-sm font-medium text-gray-700">Nama Obat</label>
                                    <input id="nama_obat" name="nama_obat" type="text" placeholder="Nama Lengkap Obat" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <div class="text-red-500 text-xs mt-1 error-text error-nama_obat"></div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="" disabled selected>-- Pilih Kategori --</option>
                                        <option value="Obat Luar">Obat makan/minum</option>
                                        <option value="Obat Makan/Minum">Alat Kesehatan</option>
                                        <option value="Alat Kesehatan">Obat Luar</option>
                                    </select>
                                    <div class="text-red-500 text-xs mt-1 error-text error-kategori"></div>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier</label>
                                    <select id="supplier" name="supplier" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="" disabled selected>-- Pilih Supplier --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->nama_supplier }}">{{ $s->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-red-500 text-xs mt-1 error-text error-supplier"></div>
                                </div>

                                <div class="md:col-span-1">
                                    <label for="stok" class="block text-sm font-medium text-gray-700">Stok (qty)</label>
                                    <input id="stok" name="stok" type="number" placeholder="0" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <div class="text-red-500 text-xs mt-1 error-text error-stok"></div>
                                </div>
                                
                                <div class="md:col-span-1">
                                    <label for="harga_satuan" class="block text-sm font-medium text-gray-700">Harga Satuan</label>
                                    <input id="harga_satuan" name="harga_satuan" type="number" placeholder="0" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <div class="text-red-500 text-xs mt-1 error-text error-harga_satuan"></div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="harga_box" class="block text-sm font-medium text-gray-700">Harga per Box</label>
                                    <input id="harga_box" name="harga_box" type="number" placeholder="0" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <div class="text-red-500 text-xs mt-1 error-text error-harga_box"></div>
                                </div>
                            </div>
                            <div class="flex space-x-4 mt-6">
                                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 font-semibold transition-colors">Simpan</button>
                                <button type="button" id="btn-clear" class="w-1/3 bg-gray-300 text-gray-700 p-2 rounded-md hover:bg-gray-400 font-semibold transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>





                    <div class="mb-4">
                        <form action="{{ route('obat.master.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="text" name="search" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="Cari berdasarkan Nama Obat..." value="{{ request('search') }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">Cari</button>
                            <a href="{{ route('obat.master.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </form>
                    </div>

                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Dibuat</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Supplier</th>
                                    <th class="py-3 px-6 text-center">Stok</th>
                                    <th class="py-3 px-6 text-right">Harga Satuan</th>
                                    <th class="py-3 px-6 text-right">Harga Box</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelObat" class="text-gray-700 text-sm ">
                                @forelse($data_obats as $obat)
                                    <tr id="row-{{ $obat->id }}" class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">{{ $obat->tanggal ? \Carbon\Carbon::parse($obat->tanggal)->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->supplier?->nama_supplier ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center font-bold">{{ $obat->stok }}</td>
                                        <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_box, 0, ',', '.') }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <button class="edit-btn bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 text-xs" data-id="{{ $obat->id }}">Edit</button>
                                            <!-- <button class="delete-btn bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-xs" data-id="{{ $obat->id }}">Hapus</button> -->
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center p-4">Belum ada data obat atau data tidak ditemukan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>


    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // --- FUNGSI BANTUAN ---
        function showNotification(message) {
            $('#notification-message').text(message);
            $('#notification').fadeIn().delay(3000).fadeOut();
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function resetForm() {
            $('#formObat')[0].reset();
            $('#obat_id').val('');
            $('.error-text').text('');
            $('#formObat button[type="submit"]').text('Simpan').removeClass('bg-green-500 hover:bg-green-600').addClass('bg-blue-500 hover:bg-blue-600');
        }

        // Fungsi untuk membuat atau memperbarui baris tabel
        function updateTableRow(obat) {
            let formattedHargaSatuan = formatRupiah(obat.harga_satuan);
            let formattedHargaBox = obat.harga_box ? formatRupiah(obat.harga_box) : '';
            let supplierName = obat.supplier ? obat.supplier.nama_supplier : 'N/A';
            let formattedDate = new Date(obat.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });

            let rowContent = `
                <td class="py-3 px-6 text-left">${formattedDate}</td>
                <td class="py-3 px-6 text-left font-medium">${obat.nama_obat}</td>
                <td class="py-3 px-6 text-left">${obat.kategori}</td>
                <td class="py-3 px-6 text-left">${supplierName}</td>
                <td class="py-3 px-6 text-center font-bold">${obat.stok}</td>
                <td class="py-3 px-6 text-right">${formattedHargaSatuan}</td>
                <td class="py-3 px-6 text-right">${formattedHargaBox}</td>
                <td class="py-3 px-6 text-center">
                    <button class="edit-btn bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600 text-xs" data-id="${obat.id}">Edit</button>
                    <button class="delete-btn bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 text-xs" data-id="${obat.id}">Hapus</button>
                </td>
            `;

            let existingRow = $(`#row-${obat.id}`);
            if (existingRow.length) {
                existingRow.html(rowContent); // Update baris yang ada
            } else {
                $('#tabelObat').append(`<tr id="row-${obat.id}" class="border-b border-gray-200 hover:bg-gray-100">${rowContent}</tr>`); // Tambah baris baru
            }
        }

        // --- EVENT LISTENERS ---
        $('#btn-clear').on('click', resetForm);

        $('#formObat').submit(function(e) {
            e.preventDefault();
            $('.error-text').text('');
            let id = $('#obat_id').val();
            let url = id ? `/master_data/${id}` : "{{ route('obat.master.store') }}";
            let method = id ? 'PUT' : 'POST';
            let formData = $(this).serialize();

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
    if(response.success){
        // Kode saat ini yang melakukan update real-time
        showNotification(response.message);
        updateTableRow(response.data);
        resetForm();
    }
},
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $(`.error-${key}`).text(value[0]);
                        });
                    } else {
                        alert('Terjadi kesalahan pada server. Silakan coba lagi.');
                    }
                }
            });
        });

        $('#tabelObat').on('click', '.edit-btn', function() {
            let row = $(this).closest('tr');
            let id = $(this).data('id');

            $('#obat_id').val(id);
            $('#nama_obat').val(row.find('td:eq(1)').text());
            $('#kategori').val(row.find('td:eq(2)').text());
            $('#supplier').val(row.find('td:eq(3)').text());
            $('#stok').val(row.find('td:eq(4)').text());
            $('#harga_satuan').val(row.find('td:eq(5)').text().replace(/[^0-9]/g, ''));
            $('#harga_box').val(row.find('td:eq(6)').text().replace(/[^0-9]/g, ''));
            
            $('#formObat button[type="submit"]').text('Update').removeClass('bg-blue-500').addClass('bg-green-500 hover:bg-green-600');
            $('html, body').animate({ scrollTop: 0 }, 'slow');
        });

        $('#tabelObat').on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: `/master_data/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.success){
                            showNotification(response.message);
                            $(`#row-${id}`).fadeOut(300, function() { $(this).remove(); });
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus data.');
                    }
                });
            }
        });
    });
    </script>
    </x-app-layout>