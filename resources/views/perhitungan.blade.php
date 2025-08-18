<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Peramalan & Rencana Pembelian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Filter & Opsi -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Filter Data Penjualan</h3>
                    <form action="{{ route('obat.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
                
                ---

                <!-- TABEL HASIL PERAMALAN (sebagai sumber data) -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-800">Hasil Perhitungan Fuzzy Mamdani</h2>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-blue-600">
                                <tr class="text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                    <th class="py-3 px-6 text-center">Penjualan Periode Ini</th>
                                    <th class="py-3 px-6 text-center font-bold">Rekomendasi</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($hasilPeramalan as $hasil)
                                <tr class="border-b border-gray-200 hover:bg-blue-50">
                                    <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-red-600">{{ $hasil['stok_saat_ini'] }}</td>
                                    <td class="py-3 px-6 text-center font-semibold">{{ $hasil['total_penjualan_periode'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-blue-700 text-lg">{{ $hasil['rekomendasi_pembelian'] }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <input type="checkbox" name="obat_terpilih[]" value="{{ $hasil['obat_id'] }}"
                                            data-nama-obat="{{ $hasil['nama_obat'] }}"
                                            data-rekomendasi="{{ $hasil['rekomendasi_pembelian'] }}"
                                            data-harga-satuan="{{ $hasil['harga_pcs'] ?? 0 }}"
                                            data-supplier-id="{{ $hasil['supplier_id'] ?? '' }}"
                                            class="form-checkbox h-5 w-5 text-indigo-600">
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center p-6 text-gray-500">Tidak ada data untuk ditampilkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulir dan Tombol Aksi Baru -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Pilih Obat untuk Pembelian</h3>
                    <div id="selected-obat-list" class="space-y-2 mb-4">
                        <p class="text-gray-500">Pilih obat dari tabel di atas untuk ditambahkan ke daftar ini.</p>
                    </div>

                    <form id="form-pembelian">
                        <input type="hidden" name="perhitungan_id" id="perhitungan_id">
                        <input type="hidden" name="perhitungan_data" id="perhitungan_data">

                        <div class="flex space-x-4">
                            <button type="button" id="btn-pilih-obat" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                                Pilih & Simpan Perhitungan
                            </button>
                            <button type="button" id="btn-proses-pembelian" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md" style="display:none;">
                                Proses Pembelian
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const hasilPeramalan = @json($hasilPeramalan);
        let selectedObat = {};

        // Event listener untuk checkbox
        $('input[name="obat_terpilih[]"]').on('change', function() {
            const obatId = $(this).val();
            const obatData = hasilPeramalan.find(o => o.obat_id == obatId);

            if ($(this).is(':checked')) {
                if (obatData) {
                    selectedObat[obatId] = obatData;
                }
            } else {
                delete selectedObat[obatId];
            }
            renderSelectedObatList();
        });

        // Tampilkan daftar obat yang dipilih
        function renderSelectedObatList() {
            const container = $('#selected-obat-list');
            container.empty();
            if (Object.keys(selectedObat).length === 0) {
                container.html('<p class="text-gray-500">Pilih obat dari tabel di atas untuk ditambahkan ke daftar ini.</p>');
                $('#btn-proses-pembelian').hide();
            } else {
                let listHtml = '<ul class="list-disc list-inside space-y-1">';
                Object.values(selectedObat).forEach(obat => {
                    listHtml += `<li><strong>${obat.nama_obat}</strong> - Rekomendasi: ${obat.rekomendasi_pembelian} pcs</li>`;
                });
                listHtml += '</ul>';
                container.html(listHtml);
                $('#btn-proses-pembelian').show();
            }
        }

        // Tombol untuk menyimpan perhitungan
        $('#btn-pilih-obat').on('click', function() {
            if (Object.keys(selectedObat).length === 0) {
                Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Pilih obat terlebih dahulu!' });
                return;
            }

            const dataToSave = {
                hasil: Object.values(selectedObat)
            };

            $.ajax({
                url: "{{ route('perhitungan.simpan') }}",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(dataToSave),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Perhitungan Tersimpan!', text: response.message });
                        $('#perhitungan_id').val(response.perhitungan_id);
                        $('#perhitungan_data').val(JSON.stringify(Object.values(selectedObat)));
                        $('#btn-proses-pembelian').show();
                    }
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan perhitungan.' });
                }
            });
        });

        // Tombol untuk memproses pembelian
       // GANTI SELURUH FUNGSI CLICK #btn-proses-pembelian ANDA DENGAN INI

// GANTI SELURUH FUNGSI CLICK #btn-proses-pembelian ANDA DENGAN INI

// GANTI SELURUH FUNGSI CLICK #btn-proses-pembelian ANDA DENGAN INI

// GANTI SELURUH FUNGSI CLICK #btn-proses-pembelian ANDA DENGAN INI

$('#btn-proses-pembelian').on('click', function() {
    const perhitunganId = $('#perhitungan_id').val();
    const obatTerpilih = Object.values(selectedObat);
    
    const suppliers = @json($suppliers ?? []); 
    const semuaObat = @json($semuaObat ?? []);

    if (!perhitunganId || obatTerpilih.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Aksi Gagal', text: 'Simpan perhitungan terlebih dahulu dan pastikan ada obat yang dipilih!' });
        return;
    }

    // -- MEMBUAT FORM HTML DINAMIS (VERSI LENGKAP) --
    let formHtml = `
        <div class="text-left mb-4">
            <label for="status_pembelian" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
            <select id="status_pembelian" class="swal2-select">
                <option value="Lunas" selected>Lunas (Cash)</option>
                <option value="Belum Lunas">Belum Lunas (Kredit)</option>
            </select>
        </div>
        <div id="form-pembelian-detail" class="space-y-4 text-left">
            <div class="grid grid-cols-12 gap-x-4 font-bold border-b pb-2 text-sm text-gray-600">
                <div class="col-span-3">Nama Obat</div>
                <div class="col-span-3">Supplier</div>
                <div class="col-span-2 text-center">Jumlah</div>
                <div class="col-span-2 text-center">Harga Satuan</div>
                <div class="col-span-2 text-center">Harga Box</div>
            </div>
    `;

    // Loop untuk menampilkan obat yang sudah direkomendasikan
    obatTerpilih.forEach(item => {
        let supplierOptions = '<option value="">Pilih Supplier</option>';
        suppliers.forEach(supplier => {
            const isSelected = supplier.id == item.supplier_id ? 'selected' : '';
            supplierOptions += `<option value="${supplier.id}" ${isSelected}>${supplier.nama_supplier}</option>`;
        });

        formHtml += `
            <div class="grid grid-cols-12 gap-x-4 items-center border-b py-2 form-row" data-obat-id="${item.obat_id}">
                <div class="col-span-3 font-semibold text-gray-700">${item.nama_obat}</div>
                <div class="col-span-3"><select name="supplier_id" class="swal2-select m-0 w-full">${supplierOptions}</select></div>
                <div class="col-span-2"><input type="number" name="jumlah" class="swal2-input m-0 w-full text-center" value="${item.rekomendasi_pembelian}"></div>
                <div class="col-span-2"><input type="number" name="harga_beli_satuan" class="swal2-input m-0 w-full text-center" value="${item.harga_pcs}"></div>
                <div class="col-span-2"><input type="number" name="harga_beli_box" class="swal2-input m-0 w-full text-center" value="${item.harga_box || 0}"></div>
            </div>
        `;
    });
    
    formHtml += `</div>`; // Penutup div #form-pembelian-detail

    formHtml += `
        <div class="text-left mt-4">
            <button type="button" id="tambah-obat-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-sm">
                + Tambah Obat Lain
            </button>
        </div>
    `;

    // -- MENAMPILKAN MODAL SWEETALERT --
    Swal.fire({
        title: 'Form Detail Pembelian',
        html: formHtml,
        width: '900px',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses Pembelian',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        didOpen: () => {
            const swal_popup = Swal.getPopup();
            $(swal_popup).on('click', '#tambah-obat-btn', function() {
                let obatOptions = '<option value="" selected>Pilih Obat</option>';
                semuaObat.forEach(obat => {
                    obatOptions += `<option value="${obat.id}">${obat.nama_obat}</option>`;
                });
                
                let supplierOptions = '<option value="" selected>Pilih Supplier</option>';
                suppliers.forEach(supplier => {
                    supplierOptions += `<option value="${supplier.id}">${supplier.nama_supplier}</option>`;
                });

                const barisBaruHtml = `
                    <div class="grid grid-cols-12 gap-x-4 items-center border-b py-2 form-row-baru">
                        <div class="col-span-3"><select name="obat_id" class="swal2-select m-0 w-full">${obatOptions}</select></div>
                        <div class="col-span-3"><select name="supplier_id" class="swal2-select m-0 w-full">${supplierOptions}</select></div>
                        <div class="col-span-2"><input type="number" name="jumlah" class="swal2-input m-0 w-full text-center" value="1"></div>
                        <div class="col-span-2"><input type="number" name="harga_beli_satuan" class="swal2-input m-0 w-full text-center" value="0"></div>
                        <div class="col-span-2"><input type="number" name="harga_beli_box" class="swal2-input m-0 w-full text-center" value="0"></div>
                    </div>
                `;
                $('#form-pembelian-detail').append(barisBaruHtml);
            });
        },
        preConfirm: () => {
            const detailItems = [];
            const formRows = document.querySelectorAll('.form-row, .form-row-baru');
            const status = document.querySelector('#status_pembelian').value;
            let isValid = true;

            formRows.forEach(row => {
                const obatSelect = row.querySelector('select[name="obat_id"]');
                const obat_id = obatSelect ? obatSelect.value : row.dataset.obatId;
                const supplier_id = row.querySelector('select[name="supplier_id"]').value;
                const jumlah = parseInt(row.querySelector('input[name="jumlah"]').value);
                const harga_beli_satuan = parseFloat(row.querySelector('input[name="harga_beli_satuan"]').value);
                const harga_beli_box = parseFloat(row.querySelector('input[name="harga_beli_box"]').value);

                if (!supplier_id || !obat_id) { isValid = false; }
                if (isNaN(jumlah) || jumlah <= 0) { isValid = false; }
                if (isNaN(harga_beli_satuan) || harga_beli_satuan < 0) { isValid = false; }
                if (isNaN(harga_beli_box) || harga_beli_box < 0) { isValid = false; }

                if (isValid) { // Hanya tambahkan jika valid untuk menghindari pengiriman data parsial
                    detailItems.push({
                        obat_id: parseInt(obat_id),
                        supplier_id: parseInt(supplier_id),
                        jumlah: jumlah,
                        harga_beli_satuan: harga_beli_satuan,
                        harga_beli_box: harga_beli_box
                    });
                }
            });

            if (!isValid) {
                Swal.showValidationMessage('Pastikan semua data (obat, supplier, jumlah, harga) untuk setiap baris sudah diisi dengan benar.');
                return false;
            }

            return { 
                detail_pembelian: detailItems,
                status: status
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const dataToSend = {
                tanggal_pembelian: new Date().toISOString().slice(0, 10),
                status: result.value.status,
                detail_pembelian: result.value.detail_pembelian
            };
            $.ajax({
                url: "{{ route('pembelian.simpan') }}",
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(dataToSend),
                success: function(response) {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                }
            });
        }
    });
});
    });
</script>
