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

                <!-- TABEL BARU: Rencana Pembelian yang bisa diedit -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-green-800">Rencana Pembelian</h2>
                    <form id="form-pembelian">
                        <div class="overflow-x-auto max-h-80"> <!-- ✅ Perubahan: max-h-80 untuk membuat tabel dapat di-scroll -->
                            <table class="w-full border-collapse">
                                <thead class="sticky top-0 bg-green-600">
                                    <tr class="text-white uppercase text-sm leading-normal">
                                        <th class="py-3 px-6 text-left">Nama Obat</th>
                                        <th class="py-3 px-6 text-center">Jumlah Pembelian</th>
                                        <th class="py-3 px-6 text-left">Supplier</th>
                                        <th class="py-3 px-6 text-right">Harga Beli Satuan</th>
                                        <th class="py-3 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm" id="rencana-pembelian-body">
                                    <tr>
                                        <td colspan="5" class="text-center p-6 text-gray-500">Pilih obat dari tabel di bawah untuk ditambahkan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" id="btn-proses-pembelian" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                                Proses Pembelian
                            </button>
                        </div>
                    </form>
                </div>
                
                ---
                
                <!-- TABEL HASIL PERAMALAN (sebagai sumber data) -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-800">Hasil Perhitungan Fuzzy Mamdani</h2>
                    <div class="overflow-x-auto max-h-96"> <!-- ✅ Perubahan: max-h-96 untuk membuat tabel dapat di-scroll -->
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
                                        <button type="button" 
                                            class="tambah-pembelian bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-1 px-3 rounded-md text-xs"
                                            data-obat-id="{{ $hasil['obat_id'] }}"
                                            data-nama-obat="{{ $hasil['nama_obat'] }}"
                                            data-rekomendasi="{{ $hasil['rekomendasi_pembelian'] }}"
                                            data-harga-satuan="{{ $hasil['harga_pcs'] ?? 0 }}"
                                            data-supplier-id="{{ $hasil['supplier_id'] ?? '' }}">
                                            Tambahkan
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center p-6 text-gray-500">Tidak ada data untuk ditampilkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

