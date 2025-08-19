<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
  <nav class="bg-green-700 text-white p-4 rounded-xl mb-6 flex justify-between items-center shadow-md">
      <h1 class="text-xl font-bold">Apotek Parakan Muncang</h1>
      <div class="space-x-4">
        <a href="/master_data" class="hover:underline">Master Data</a>
        <a href="/perhitungan" class="hover:underline">Pembelian</a>
        <a href="/penjualan" class="hover:underline">Transaksi</a>
        <a href="/cek" class="hover:underline">Pengecekan stok</a>
        <a href="#" class="font-bold underline">Karyawan</a>
      </div>
  </nav>
  <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-4">Data Karyawan</h1>
    <div class="bg-gray-50 p-6 rounded-lg mb-8 border border-gray-200">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Tambah Karyawan Baru</h2>
        <form>
            <div class="grid md:grid-cols-4 grid-cols-2 gap-4">
                <input type="text" placeholder="Nama Lengkap" class="border p-2 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" disabled>
                <input type="text" placeholder="Posisi Jabatan" class="border p-2 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" disabled>
                <input type="tel" placeholder="Nomor Telepon" class="border p-2 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" disabled>
                <input type="text" placeholder="Alamat" class="border p-2 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none" disabled>
            </div>
            <button type="button" class="mt-4 w-full bg-blue-400 text-white p-2 rounded-md cursor-not-allowed">
                Simpan (Nonaktif)
            </button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">No</th>
                    <th class="py-3 px-6 text-left">Nama Karyawan</th>
                    <th class="py-3 px-6 text-left">Posisi</th>
                    <th class="py-3 px-6 text-left">Nomor Telepon</th>
                    <th class="py-3 px-6 text-left">Alamat</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">1</td>
                    <td class="py-3 px-6 text-left font-medium">Andi Budiman</td>
                    <td class="py-3 px-6 text-left">Apoteker Penanggung Jawab</td>
                    <td class="py-3 px-6 text-left">0812-3456-7890</td>
                    <td class="py-3 px-6 text-left">Jl. Merdeka No. 10, Bandung</td>
                    <td class="py-3 px-6 text-center">
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Edit</button>
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Hapus</button>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">2</td>
                    <td class="py-3 px-6 text-left font-medium">Siti Aminah</td>
                    <td class="py-3 px-6 text-left">Asisten Apoteker</td>
                    <td class="py-3 px-6 text-left">0857-1234-5678</td>
                    <td class="py-3 px-6 text-left">Jl. Cendrawasih No. 5, Bandung</td>
                     <td class="py-3 px-6 text-center">
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Edit</button>
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Hapus</button>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">3</td>
                    <td class="py-3 px-6 text-left font-medium">Bambang Susanto</td>
                    <td class="py-3 px-6 text-left">Kasir</td>
                    <td class="py-3 px-6 text-left">0811-9876-5432</td>
                    <td class="py-3 px-6 text-left">Komp. Permata Biru Blok C1 No. 12</td>
                     <td class="py-3 px-6 text-center">
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Edit</button>
                        <button class="bg-gray-400 text-white py-1 px-3 rounded cursor-not-allowed text-xs">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
  </div>
</body>
</html>
