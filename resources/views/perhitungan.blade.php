<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komprehensif Stok & Peramalan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<nav class="bg-green-700 text-white p-4 rounded-xl mb-4 flex justify-between items-center">
  <h1 class="text-xl font-bold">Apotek Parakan Muncang</h1>
  <div class="space-x-4">
    <a href="/master_data" class="hover:underline">Master Data</a>
    <a href="/perhitungan" class="font-bold underline">Pembelian</a>
    <a href="/penjualan" class="hover:underline">Transaksi</a>
    <a href="/cek" class="hover:underline">Pengecekan stok</a>
    <a href="/karyawan" class="hover:underline">Karyawan</a>
  </div>
</nav>
<div class="max-w-7xl mx-auto bg-white p-8 rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Laporan Komprehensif Stok & Peramalan</h1>

    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4 text-blue-800">1. Hasil Perhitungan</h2>
        <div class="overflow-y-auto max-h-80">
            <table class="w-full border-collapse border border-blue-300">
                <thead>
                    <tr class="bg-blue-600 text-white uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Nama Obat</th>
                        <th class="py-3 px-6 text-center">Stok Saat Ini</th>
                        <th class="py-3 px-6 text-center">Penjualan Terakhir</th>
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
                            <td class="py-3 px-6 text-center">{{ $hasil['penjualan_terakhir_input'] }}</td>
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

    <!-- Tabel 2: Rekapitulasi Stok Kurang dari 20 -->
   
    

</div>

</body>
</html>
