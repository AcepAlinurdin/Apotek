<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Peramalan & Rencana Pembelian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="mb-6 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-1">Rekomendasi Pembelian Berikutnya</h3>
                    <p class="text-sm text-gray-600">
                        Perhitungan ini dibuat berdasarkan data penjualan pada periode tetap:
                        <strong>1 Oktober 2023</strong> sampai <strong>31 Desember 2023</strong>.
                    </p>
                </div>
                
                <!-- TABEL 1: HASIL PERHITUNGAN FUZZY -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-800">Hasil Perhitungan Fuzzy (Obat Stok Menipis)</h2>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-blue-600">
                                <tr class="text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                    <th class="py-3 px-6 text-center">Penjualan Periode</th>
                                    <th class="py-3 px-6 text-center font-bold">Rekomendasi</th>
                                    <!-- [MODIFIED] Kolom baru ditambahkan di sini -->
                                    <th class="py-3 px-6 text-center">Kategori Pembelian</th>
                                    <th class="py-3 px-6 text-center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($hasilPeramalan as $hasil)
                                <tr class="border-b border-gray-200 hover:bg-blue-50 bg-yellow-100">
                                    <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-red-600">{{ $hasil['stok_saat_ini'] }}</td>
                                    <td class="py-3 px-6 text-center font-semibold">{{ $hasil['total_penjualan_periode'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-blue-700 text-lg">{{ $hasil['rekomendasi_pembelian'] }}</td>
                                    
                                    <!-- [MODIFIED] Logika untuk menampilkan kategori pembelian -->
                                    <td class="py-3 px-6 text-center">
                                        @php
                                            $rekomendasi = $hasil['rekomendasi_pembelian'];
                                            $kategori = '';
                                            $bgColor = '';
                                            if ($rekomendasi >= 1 && $rekomendasi <= 20) {
                                                $kategori = 'Sedikit';
                                                $bgColor = 'bg-yellow-200 text-yellow-800';
                                            } elseif ($rekomendasi >= 21 && $rekomendasi <= 30) {
                                                $kategori = 'Normal';
                                                $bgColor = 'bg-green-200 text-green-800';
                                            } elseif ($rekomendasi >= 31 && $rekomendasi <= 50) {
                                                $kategori = 'Banyak';
                                                $bgColor = 'bg-blue-200 text-blue-800';
                                            } else {
                                                $kategori = 'Sangat Banyak'; // Kategori tambahan jika > 50
                                                $bgColor = 'bg-red-200 text-red-800';
                                            }
                                        @endphp
                                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs {{ $bgColor }}">
                                            {{ $kategori }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-6 text-center">
                                        <input type="checkbox" name="obat_terpilih[]" value="{{ $hasil['obat_id'] }}"
                                               data-nama-obat="{{ $hasil['nama_obat'] }}"
                                               data-rekomendasi="{{ $hasil['rekomendasi_pembelian'] }}"
                                               data-harga-satuan="{{ $hasil['harga_pcs'] ?? 0 }}"
                                               data-harga-box="{{ $hasil['harga_box'] ?? 0 }}"
                                               data-supplier-id="{{ $hasil['supplier_id'] ?? '' }}"
                                               class="form-checkbox h-5 w-5 text-indigo-600">
                                    </td>
                                </tr>
                                @empty
                                <!-- [MODIFIED] Colspan diubah menjadi 7 -->
                                <tr><td colspan="7" class="text-center p-6 text-gray-500">Tidak ada data obat dengan stok menipis untuk dihitung.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
    
                <!-- KERANJANG RENCANA PEMBELIAN -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow-inner">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Keranjang Rencana Pembelian (Dari Rekomendasi)</h3>
                    <div id="selected-obat-list" class="space-y-2 mb-4 min-h-[50px]">
                        <p class="text-gray-500">Pilih obat dari tabel di atas untuk ditambahkan ke sini.</p>
                    </div>

                    <div class="flex space-x-4">
                        
                            {{-- Tampilan untuk Kepala Apotek: Dua Tombol --}}
                            <button type="button" id="btn-buat-rencana" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                                Buat Rincian (Rekomendasi)
                            </button>
                            <button type="button" id="btn-pembelian-manual" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                                Pembelian Manual
                            </button>
                        
                    </div>
                </div>

                <hr class="my-12 border-t-2 border-gray-200">

                
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Daftar Stok Semua Obat</h2>
                    <div class="overflow-x-auto" style="max-height: 500px;">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-gray-600">
                                <tr class="text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($semuaObatList as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 {{ $obat->stok < 21 ? 'bg-yellow-100' : '' }}">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                        <td class="py-3 px-6 text-center font-bold {{ $obat->stok < 21 ? 'text-red-600' : 'text-gray-700' }}">{{ $obat->stok }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center p-6 text-gray-500">Tidak ada data obat di database.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

        // Variabel global
        const hasilPeramalan = @json($hasilPeramalan);
        const suppliers = @json($suppliers ?? []);
        const semuaObat = @json($semuaObat ?? []);
        let selectedObat = {};

        // =====================================================================
        // == LOGIKA UNTUK PEMBELIAN BERDASARKAN REKOMENDASI FUZZY
        // =====================================================================

        $('input[name="obat_terpilih[]"]').on('change', function() {
            const obatId = $(this).val();
            const obatData = hasilPeramalan.find(o => o.obat_id == obatId);

            if ($(this).is(':checked')) {
                if (obatData) selectedObat[obatId] = obatData;
            } else {
                delete selectedObat[obatId];
            }
            renderSelectedObatList();
        });

        function renderSelectedObatList() {
            const container = $('#selected-obat-list');
            container.empty();
            if (Object.keys(selectedObat).length === 0) {
                container.html('<p class="text-gray-500">Pilih obat dari tabel di atas untuk ditambahkan ke sini.</p>');
            } else {
                let listHtml = '<ul class="list-disc list-inside space-y-1">';
                Object.values(selectedObat).forEach(obat => {
                    listHtml += `<li><strong>${obat.nama_obat}</strong> - Rekomendasi: ${obat.rekomendasi_pembelian} pcs</li>`;
                });
                listHtml += '</ul>';
                container.html(listHtml);
            }
        }
        
        $('#btn-buat-rencana').on('click', function() {
            const obatTerpilih = Object.values(selectedObat);
            if (obatTerpilih.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Pilih minimal satu obat dari tabel rekomendasi!' });
                return;
            }
            // Memanggil fungsi utama untuk menampilkan form
            showPurchaseForm(obatTerpilih, true);
        });

        // =====================================================================
        // == LOGIKA BARU UNTUK TOMBOL PEMBELIAN MANUAL
        // =====================================================================
        $('#btn-pembelian-manual').on('click', function() {
            // Memanggil fungsi utama dengan array kosong, karena dimulai dari nol
            showPurchaseForm([], false);
        });


        // =====================================================================
        // == FUNGSI UTAMA UNTUK MENAMPILKAN FORM DAN MEMPROSES PEMBELIAN
        // =====================================================================
        function showPurchaseForm(items, isFromRekomendasi) {
            let formHtml = `
                <div class="text-left mb-4">
                    <label for="status_pembelian" class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                    <select id="status_pembelian" class="swal2-select">
                        <option value="Lunas" selected>Lunas (Cash)</option>
                        <option value="Belum Lunas">Belum Lunas (Kredit)</option>
                    </select>
                </div>
                <div id="form-pembelian-detail" class="space-y-4 text-left">
            `;

            // Render item yang sudah ada (dari rekomendasi)
            items.forEach(item => {
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
            formHtml += `</div>`; 

            // Tombol untuk menambah obat lain
            formHtml += `
                <div class="text-left mt-4">
                    <button type="button" id="tambah-obat-btn" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-3 rounded text-sm">
                        + Tambah Obat Lain
                    </button>
                </div>
            `;

            Swal.fire({
                title: isFromRekomendasi ? 'Form Rincian Pembelian' : 'Form Pembelian Manual',
                html: formHtml,
                width: '900px',
                showCancelButton: true,
                confirmButtonText: 'Ya, Proses Pembelian',
                cancelButtonText: 'Batal',
                focusConfirm: false,
                didOpen: () => {
                    const popup = Swal.getPopup();
                    
                    // Fungsi untuk menambah baris baru
                    function addNewRow() {
                        let obatOptions = '<option value="" selected>-- Pilih Obat --</option>';
                        semuaObat.forEach(obat => {
                            obatOptions += `<option value="${obat.id}" data-supplier-id="${obat.supplier_id}" data-harga-satuan="${obat.harga_satuan}" data-harga-box="${obat.harga_box || 0}">${obat.nama_obat}</option>`;
                        });
                        
                        let supplierOptions = '<option value="" selected>-- Pilih Supplier --</option>';
                        suppliers.forEach(supplier => {
                            supplierOptions += `<option value="${supplier.id}">${supplier.nama_supplier}</option>`;
                        });

                        const barisBaruHtml = `
                            <div class="grid grid-cols-12 gap-x-4 items-center border-b py-2 form-row-baru">
                                <div class="col-span-3"><select name="obat_id" class="swal2-select m-0 w-full obat-select-baru">${obatOptions}</select></div>
                                <div class="col-span-3"><select name="supplier_id" class="swal2-select m-0 w-full supplier-select-baru">${supplierOptions}</select></div>
                                <div class="col-span-2"><input type="number" name="jumlah" class="swal2-input m-0 w-full text-center" value="1"></div>
                                <div class="col-span-2"><input type="number" name="harga_beli_satuan" class="swal2-input m-0 w-full text-center" value="0"></div>
                                <div class="col-span-1"><input type="number" name="harga_beli_box" class="swal2-input m-0 w-full text-center" value="0"></div>
                                <div class="col-span-1 text-center"><button type="button" class="text-red-500 hover:text-red-700 hapus-baris-btn font-bold">X</button></div>
                            </div>
                        `;
                        $('#form-pembelian-detail').append(barisBaruHtml);
                    }

                    // Jika ini form manual, langsung tambahkan satu baris kosong
                    if (!isFromRekomendasi) {
                        addNewRow();
                    }

                    $(popup).on('click', '#tambah-obat-btn', addNewRow);

                    $(popup).on('change', '.obat-select-baru', function() {
                        const selectedOption = $(this).find('option:selected');
                        const row = $(this).closest('.form-row-baru');
                        row.find('.supplier-select-baru').val(selectedOption.data('supplier-id'));
                        row.find('input[name="harga_beli_satuan"]').val(selectedOption.data('harga-satuan'));
                        row.find('input[name="harga_beli_box"]').val(selectedOption.data('harga-box'));
                    });

                    $(popup).on('click', '.hapus-baris-btn', function() {
                        $(this).closest('.form-row-baru').remove();
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

                        if (!supplier_id || !obat_id || isNaN(jumlah) || jumlah <= 0 || isNaN(harga_beli_satuan) || harga_beli_satuan < 0) {
                            isValid = false;
                        }

                        if (isValid) { 
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
                        Swal.showValidationMessage('Pastikan semua data (obat, supplier, jumlah, harga) diisi dengan benar.');
                        return false;
                    }
                    if (detailItems.length === 0) {
                        Swal.showValidationMessage('Tidak ada item untuk dibeli.');
                        return false;
                    }
                    return { detail_pembelian: detailItems, status: status };
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
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    showCancelButton: true,
                                    confirmButtonText: 'OK',
                                    cancelButtonText: 'Cetak Bukti',
                                    cancelButtonColor: '#1e40af'
                                }).then((swalResult) => {
                                    if (swalResult.isDismissed && swalResult.dismiss === Swal.DismissReason.cancel) {
                                        printReceipt(response.pembelian_id, result.value, suppliers);
                                    }
                                    window.location.reload();
                                });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                        }
                    });
                }
            });
        }

        function printReceipt(pembelianId, purchaseData, suppliers) {
            let totalHarga = 0;
            let detailRows = '';
            
            purchaseData.detail_pembelian.forEach((item, index) => {
                const subtotal = item.jumlah * item.harga_beli_satuan;
                totalHarga += subtotal;
                // Cari nama obat dari array 'semuaObat' karena 'selectedObat' mungkin tidak lengkap
                const obatInfo = semuaObat.find(o => o.id == item.obat_id);
                const namaObat = obatInfo ? obatInfo.nama_obat : 'N/A';

                detailRows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${namaObat}</td>
                        <td class="text-right">${item.jumlah}</td>
                        <td class="text-right">Rp ${number_format(item.harga_beli_satuan)}</td>
                        <td class="text-right">Rp ${number_format(subtotal)}</td>
                    </tr>
                `;
            });

            const supplierId = purchaseData.detail_pembelian[0]?.supplier_id;
            const supplier = suppliers.find(s => s.id == supplierId);
            const namaSupplier = supplier ? supplier.nama_supplier : 'N/A';
            const tanggalPembelian = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            
            const receiptHtml = `
                <html>
                <head>
                    <title>Bukti Pembelian - Transaksi #${pembelianId}</title>
                    <style>
                        body { font-family: 'Arial', sans-serif; margin: 20px; color: #333; font-size: 14px; }
                        .container { max-width: 800px; margin: auto; }
                        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .header h1 { margin: 0; font-size: 24px; }
                        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
                        .details-grid div strong { display: block; margin-bottom: 5px; color: #555; font-size: 12px; text-transform: uppercase; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                        th { background-color: #f2f2f2; font-weight: bold; }
                        .text-right { text-align: right; }
                        .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #333; }
                        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header"><h1>APOTEK PARAKAN MUNCANG</h1></div>
                        <h2>Bukti Pembelian Obat</h2>
                        <div class="details-grid">
                            <div><strong>Supplier:</strong><span>${namaSupplier}</span></div>
                            <div><strong>Tanggal:</strong><span>${tanggalPembelian}</span></div>
                            <div><strong>No. Transaksi:</strong><span>PEM-${String(pembelianId).padStart(5, '0')}</span></div>
                            <div><strong>Status:</strong><span>${purchaseData.status}</span></div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th class="text-right">Jumlah (pcs)</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>${detailRows}</tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="4" class="text-right">TOTAL PEMBAYARAN</td>
                                    <td class="text-right">Rp ${number_format(totalHarga)}</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="footer"><p>Dicetak pada ${new Date().toLocaleString('id-ID')}</p></div>
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(receiptHtml);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }

        function number_format(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    });
</script>
