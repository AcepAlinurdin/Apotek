<x-app-layout>
    {{-- Slot untuk judul halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Stok Obat') }}
        </h2>
    </x-slot>

    {{-- Konten utama halaman --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <!-- Tabel 1: Daftar Keseluruhan Obat dengan Status -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">1. Daftar Keseluruhan Stok Obat</h2>
                    <p class="mb-4 text-sm text-gray-600">Tabel ini menampilkan semua obat yang terdaftar beserta status ketersediaan stoknya.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Stok Akhir</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($semua_obat as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-center font-bold">{{ $obat->stok }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($obat->stok < 20)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Stok Menipis
                                                </span>
                                            @elseif($obat->stok >= 20 && $obat->stok <= 50)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Normal
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Stok Terlalu Banyak
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4">Tidak ada data obat di database.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel 2: Rekapitulasi Stok Kritis -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4 text-red-600">2. Daftar Obat Stok Kritis (&lt; 20)</h2>
                     <p class="mb-4 text-sm text-gray-600">Tabel ini hanya menampilkan obat-obatan yang stoknya menipis dan perlu segera dipesan kembali.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-red-300">
                            <thead>
                                <tr class="bg-red-200 text-red-800 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($stok_kurang as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-red-50">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-200">
                                        <td colspan="3" class="text-center p-6 text-gray-500">Aman! Tidak ada obat dengan stok di bawah 20.</td>
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
