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

                {{-- Konten yang sudah Anda buat sebelumnya dimulai di sini --}}

                <!-- Tabel 1: Rekapitulasi Stok Kurang dari 20 -->
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-red-600">Rekapitulasi Obat dengan Stok Kritis (&lt; 20)</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-red-300">
                            <thead>
                                <tr class="bg-red-200 text-red-800 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-left">Supplier</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($stok_kurang as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-red-50">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->supplier->nama_supplier ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center font-bold text-red-600">{{ $obat->stok }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-200">
                                        <td colspan="4" class="text-center p-6 text-gray-500">Aman! Tidak ada obat dengan stok di bawah 20.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel 2: Daftar Semua Obat -->
                <div>
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">Daftar Keseluruhan Obat</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-left">Supplier</th>
                                    <th class="py-3 px-6 text-center">Stok (qty)</th>
                                    <th class="py-3 px-6 text-right">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($semua_obat as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->supplier->nama_supplier ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center font-bold @if($obat->stok < 20) text-red-600 @endif">{{ $obat->stok }}</td>
                                        <td class="py-3 px-6 text-right">Rp {{ number_format($obat->harga_satuan, 0, ',', '.') }}</td>
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

            </div>
        </div>
    </div>
</x-app-layout>
