<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Peramalan & Rekomendasi Pembelian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="mb-6 bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Filter & Opsi</h3>
                    <form action="{{ route('obat.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tanggalMulai }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $tanggalAkhir }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Terapkan</button>
                        </div>
                    </form>
                    <div class="mt-4 flex justify-between items-center">
                        @if($selectedObatId)
                            <a href="{{ route('obat.rekap', ['tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold text-sm rounded-md">
                                ← Kembali ke Daftar Lengkap
                            </a>
                        @endif
                        <button type="button" id="simpan-perhitungan-btn" class="w-full md:w-auto bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md ml-auto">
                            Simpan Hasil Perhitungan
                        </button>
                    </div>
                </div>

                <div class="mb-12">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-800">Hasil Perhitungan Menggunakan Fuzzy Mamdani</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-blue-600 text-white uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">No</th>
                                    <th class="py-3 px-6 text-left">Nama Obat</th>
                                    <th class="py-3 px-6 text-center">Sisa Stok</th>
                                    <th class="py-3 px-6 text-center">Penjualan Periode Ini</th>
                                    <th class="py-3 px-6 text-center font-bold">Rekomendasi Pembelian</th>
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
                                        {{-- ✅ PASTIKAN LINK INI MENGGUNAKAN ROUTE 'obat.rekap' --}}
                                        <a href="{{ route('obat.rekap', ['obat_id' => $hasil['obat_id'], 'tanggal_mulai' => $tanggalMulai, 'tanggal_akhir' => $tanggalAkhir]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center p-6 text-gray-500">Tidak ada data untuk ditampilkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-12">
                     <h2 class="text-2xl font-semibold mb-4 text-green-800">Rencana Pembelian ke Supplier</h2>
                     <div class="overflow-x-auto">
                         <table class="w-full border-collapse">
                             <thead>
                                 <tr class="bg-green-600 text-white uppercase text-sm leading-normal">
                                     <th class="py-3 px-6 text-left">No</th>
                                     <th class="py-3 px-6 text-left">Nama Obat</th>
                                     <th class="py-3 px-6 text-left">Supplier</th>
                                     <th class="py-3 px-6 text-center">Total Pembelian</th>
                                     <th class="py-3 px-6 text-right">Harga/Box</th>
                                     <th class="py-3 px-6 text-right">Harga/Pcs</th>
                                 </tr>
                             </thead>
                             <tbody class="text-gray-700 text-sm">
                                @forelse($hasilPeramalan as $hasil)
                                    @if($hasil['rekomendasi_pembelian'] > 0)
                                    <tr class="border-b border-gray-200 hover:bg-green-50">
                                        <td class="py-3 px-6 text-left">{{ $loop->iteration }}</td>
                                        <td class="py-3 px-6 text-left font-medium">{{ $hasil['nama_obat'] }}</td>
                                        <td class="py-3 px-6 text-left">{{ $hasil['supplier'] ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center font-semibold">{{ $hasil['rekomendasi_pembelian'] }}</td>
                                        <td class="py-3 px-6 text-right">{{ isset($hasil['harga_box']) ? 'Rp ' . number_format($hasil['harga_box'], 0, ',', '.') : 'N/A' }}</td>
                                        <td class="py-3 px-6 text-right">{{ isset($hasil['harga_pcs']) ? 'Rp ' . number_format($hasil['harga_pcs'], 0, ',', '.') : 'N/A' }}</td>
                                    </tr>
                                    @endif
                                @empty
                                <tr><td colspan="6" class="text-center p-6 text-gray-500">Tidak ada rekomendasi pembelian pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                         </table>
                     </div>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
    <script>
    $(document).ready(function() {
        let hasilPeramalan = @json($hasilPeramalan);

        // ✅ PASTIKAN TOMBOL SIMPAN INI MEMILIKI 'click' LISTENER
        $('#simpan-perhitungan-btn').on('click', function() {
            if (hasilPeramalan.length === 0) {
                alert('Tidak ada data untuk disimpan.');
                return;
            }
            if (confirm('Apakah Anda yakin ingin menyimpan hasil perhitungan ini ke database?')) {
                $.ajax({
                    url: "{{ route('perhitungan.simpan') }}", // URL POST
                    type: 'POST', // METHOD POST
                    contentType: 'application/json',
                    data: JSON.stringify({ hasil: hasilPeramalan }),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.message || 'Terjadi kesalahan.');
                    }
                });
            }
        });
    });
    </script>
    @endpush
</x-app-layout>