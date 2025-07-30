<x-app-layout>
    {{-- Slot untuk judul halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Peramalan & Rekomendasi Pembelian') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- [NEW] Form untuk Filter Periode Perhitungan --}}
                <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Pilih Periode Data Penjualan</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih rentang bulan data penjualan yang akan digunakan sebagai dasar untuk meramal kebutuhan bulan berikutnya.</p>
                    <form action="{{ route('obat.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        
                        {{-- Input Bulan Mulai --}}
                        <div>
                            <label for="start_month" class="block text-sm font-medium text-gray-700">Dari Bulan</label>
                            <input type="month" id="start_month" name="start_month" value="{{ request('start_month', now()->subMonths(3)->format('Y-m')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- Input Bulan Selesai --}}
                        <div>
                            <label for="end_month" class="block text-sm font-medium text-gray-700">Sampai Bulan</label>
                            <input type="month" id="end_month" name="end_month" value="{{ request('end_month', now()->subMonth()->format('Y-m')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex space-x-2">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Hitung Peramalan
                            </button>
                            <a href="{{ route('obat.rekap') }}"
                                class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Hasil Perhitungan</h1>

                {{-- Tabel Hasil Perhitungan Fuzzy Mamdani --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-800">Hasil Perhitungan Menggunakan Fuzzy Mamdani</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-blue-300">
                            <thead>
                                <tr class="bg-blue-600 text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                    <th class="py-3 px-6 text-center">Hasil Perhitungan Fuzzy</th>
                                    <th class="py-3 px-6 text-center font-bold">Jumlah Pembelian ke Supplier</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($hasilPeramalan as $hasil)
                                <tr class="border-b border-gray-200 hover:bg-blue-50">
                                    <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-6 text-left">{{ $hasil['kategori'] }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-red-600">{{ $hasil['stok_saat_ini'] }}</td>
                                    <td class="py-3 px-6 text-center font-semibold">{{ $hasil['rekomendasi_pembelian'] }}</td>
                                    <td class="py-3 px-6 text-center font-bold text-blue-700 text-lg">
                                        {{ $hasil['rekomendasi_pembelian'] }}
                                    </td>
                                </tr>
                                @empty
                                <tr class="border-b border-gray-200">
                                    <td colspan="6" class="text-center p-6 text-gray-500">
                                        Tidak ada obat yang perlu diramal atau silakan pilih periode dan klik "Hitung Peramalan".
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-green-800">Rencana Pembelian ke Supplier</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-green-300">
                            <thead>
                                <tr class="bg-green-600 text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-left">Supplier</th>
                                    <th class="py-3 px-6 text-center">Total Pembelian ke Supplier</th>
                                    <th class="py-3 px-6 text-right">Harga/Box</th>
                                    <th class="py-3 px-6 text-right">Harga/Pcs</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($hasilPeramalan as $hasil)
                                <tr class="border-b border-gray-200 hover:bg-green-50">
                                    <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-6 text-left">{{ $hasil['kategori'] }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $hasil['supplier'] ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-center font-semibold">{{ $hasil['rekomendasi_pembelian'] }}</td>
                                    <td class="py-3 px-6 text-right">
                                        {{ isset($hasil['harga_box']) ? 'Rp ' . number_format($hasil['harga_box'], 0, ',', '.') : 'N/A' }}
                                    </td>
                                    <td class="py-3 px-6 text-right">
                                        {{ isset($hasil['harga_pcs']) ? 'Rp ' . number_format($hasil['harga_pcs'], 0, ',', '.') : 'N/A' }}
                                    </td>
                                </tr>
                                @empty
                                <tr class="border-b border-gray-200">
                                    <td colspan="7" class="text-center p-6 text-gray-500">
                                        Tidak ada data pembelian untuk ditampilkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
