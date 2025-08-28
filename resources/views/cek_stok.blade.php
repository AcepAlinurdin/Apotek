<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Stok Obat') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <form action="{{ route('obat.stok') }}" method="GET" class="flex items-end space-x-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Filter Berdasarkan Status</label>
                            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Semua Status</option>
                                <option value="menipis" {{ ($statusFilter ?? '') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                                <option value="normal" {{ ($statusFilter ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="banyak" {{ ($statusFilter ?? '') == 'banyak' ? 'selected' : '' }}>Stok Terlalu Banyak</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md mt-6">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-gray-700">1. Daftar Keseluruhan Stok Obat</h2>
                    <p class="mb-4 text-sm text-gray-600">Tabel ini menampilkan semua obat yang terdaftar beserta status ketersediaan stoknya.</p>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Stok Akhir</th>
                                    <th class="py-3 px-6 text-center">Satuan</th>
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
                                        <td class="py-3 px-6 text-center">{{ $obat->satuan }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($obat->stok < 20)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Stok Menipis
                                                </span>
                                            @elseif($obat->stok >= 21 && $obat->stok <= 70)
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
                                        <td colspan="5" class="text-center p-4">Tidak ada data obat yang cocok dengan filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-semibold mb-4 text-red-600">2. Daftar Obat Stok Kritis (&lt; 20)</h2>
                    <p class="mb-4 text-sm text-gray-600">Tabel ini hanya menampilkan obat-obatan yang stoknya menipis dan perlu segera dipesan kembali.</p>
                    <div class="overflow-x-auto max-h-96">
                        <table class="w-full border-collapse border border-red-300">
                            <thead>
                                <tr class="bg-red-200 text-red-800 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Kategori</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                    <th class="py-3 px-6 text-center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                @forelse($stok_kurang as $obat)
                                    <tr class="border-b border-gray-200 hover:bg-red-50">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left">{{ $obat->kategori }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $obat->nama_obat }}</td>
                                        <td class="py-3 px-6 text-center font-bold">{{ $obat->stok }}</td>
                                        <td class="py-3 px-6 text-center">{{ $obat->satuan }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-b border-gray-200">
                                        <td colspan="4" class="text-center p-6 text-gray-500">Aman! Tidak ada obat dengan stok di bawah 21.</td>
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