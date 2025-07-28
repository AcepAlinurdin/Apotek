<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komprehensif Stok & Peramalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 p-8">
    <nav class="bg-green-700 text-white p-4 rounded-xl mb-4 flex justify-between items-center">
        <div class="space-x-4">
            <a href="/master_data" class="hover:underline">Master Data</a>
            <a href="/penjualan" class="hover:underline">Transaksi</a>
            <a href="/cek" class="hover:underline">Pengecekan stok</a>
            <a href="/perhitungan" class="font-bold underline">Pembelian</a>
        </div>
        <h1 class="text-xl font-bold">Apotek Parakan Muncang</h1>
    </nav>
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-medium text-gray-800 mb-2">Cari Obat</h3>

        {{-- Form pencarian akan mengirim data ke URL halaman ini sendiri dengan metode GET --}}
        <form action="{{ url('/perhitungan') }}" method="GET" class="flex items-center space-x-2">
            <input type="text" name="search"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                placeholder="Ketik nama obat..." value="{{ $searchTerm ?? '' }}">
            {{-- Tampilkan kembali kata kunci yang dicari --}}

            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Cari
            </button>

            {{-- Tombol Reset (opsional, tapi sangat membantu) --}}
            <a href="{{ url('/perhitungan') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Reset
            </a>
        </form>
    </div>

    <div class=" mx-auto bg-white p-8 rounded-lg shadow-xl">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Jumlah pembelian</h1>

        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-blue-800">1. Hasil Perhitungan</h2>
            <div class="overflow-y-auto max-h-80">
                <table class="w-full border-collapse border border-blue-300">
                    <thead>
                        <tr class="bg-blue-600 text-white uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Nama Obat</th>
                            <th class="py-3 px-6 text-center">Stok</th>
                            <th class="py-3 px-6 text-center">Total Penjualan</th>
                            <th class="py-3 px-6 text-center font-bold">Rekomendasi Pembelian</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        @forelse($hasilPeramalan as $hasil)
                        <tr class="border-b border-gray-200 hover:bg-blue-50">
                            <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                            <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                            <td class="py-3 px-6 text-center font-bold text-red-600">{{ $hasil['stok_saat_ini'] }}</td>
                            <td class="py-3 px-6 text-center font-semibold">{{ $hasil['total_penjualan'] }}</td>
                            <td class="py-3 px-6 text-center font-bold text-blue-700 text-lg">
                                {{ $hasil['rekomendasi_pembelian'] }} unit
                            </td>
                            
                        </tr>
                        @empty
                        <tr class="border-b border-gray-200">
                            <td colspan="6" class="text-center p-6 text-gray-500">
                                Tidak ada obat yang perlu diramal (semua stok di atas atau sama dengan 20).
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4 text-green-800">2. Total Pembelian</h2>
            <div class="overflow-y-auto max-h-80    ">
                <table class="w-full border-collapse border border-green-300">
                    <thead>
                        <tr class="bg-green-600 text-white uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">No</th>
                            <th class="py-3 px-6 text-left">Nama Obat</th>
                            <th class="py-3 px-6 text-left">Supplier</th>
                            <th class="py-3 px-6 text-center">Total Pembelian</th>
                            <th class="py-3 px-6 text-left">Kategori</th>
                            <th class="py-3 px-6 text-right">Harga/Box</th>
                            <th class="py-3 px-6 text-right">Harga/Pcs</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm">
                        {{-- Loop ini mengasumsikan $hasilPeramalan juga berisi data supplier, kategori, dan harga. --}}
                        {{-- Anda mungkin perlu join tabel di controller untuk mendapatkan data ini. --}}
                        @forelse($hasilPeramalan as $hasil)
                        <tr class="border-b border-gray-200 hover:bg-green-50">
                            <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                            <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $hasil['supplier'] ?? 'Data Supplier Belum Ada' }}</td>
                            <td class="py-3 px-6 text-center font-semibold">{{ $hasil['rekomendasi_pembelian'] }} unit</td>
                            <td class="py-3 px-6 text-left">{{ $hasil['kategori'] ?? 'Data Kategori Belum Ada' }}</td>
                            <td class="py-3 px-6 text-right">
                                {{-- Ganti dengan data harga box yang sebenarnya, contoh: --}}
                                {{-- Rp {{ number_format($hasil['harga_box'], 0, ',', '.') }} --}}
                                Data Harga Belum Ada
                            </td>
                            <td class="py-3 px-6 text-right">
                                 {{-- Ganti dengan data harga pcs yang sebenarnya, contoh: --}}
                                {{-- Rp {{ number_format($hasil['harga_pcs'], 0, ',', '.') }} --}}
                                Data Harga Belum Ada
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

</body>

</html>
```