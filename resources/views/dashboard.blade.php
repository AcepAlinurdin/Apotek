<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> 
                {{-- Tampilan Default untuk semua user --}}
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-4 text-2xl">
                        Selamat Datang di Aplikasi Apotek!
                    </div>
                    <div class="mt-2 text-gray-500">
                        Anda login sebagai: 
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ Str::title(auth()->user()->roles->first()?->name) }}
                        </span>
                    </div>
                </div>
                @role('admin|kepala apotek')
                <div class="p-6 sm:px-20 bg-gray-50 grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kolom 1: Laporan Penjualan Dinamis --}}
                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">
                            {{ $laporanPenjualanTitle }}
                        </h3>
                        <form id="form-filter-penjualan" class="mb-4 p-4 bg-gray-100 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="tanggal" class="text-sm font-medium">Per Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" value="{{ $filterTanggal }}" class="w-full border-gray-300 rounded-md shadow-sm ">
                                </div>
                                <div>
                                    <label for="bulan" class="text-sm font-medium">Per Bulan</label>
                                    <select name="bulan" id="bulan" class="w-full border-gray-300 rounded-md shadow-sm">
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $filterBulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="tahun" class="text-sm font-medium">Tahun</label>
                                    <select name="tahun" id="tahun" class="w-full border-gray-300 rounded-md shadow-sm">
                                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}" {{ $filterTahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="self-end">
                                    <button type="button" class="btn-filter w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-md mt-6">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="bg-green-100 border border-green-300 p-4  mb-4">
                            <h4 class="font-semibold text-green-800">Total Pendapatan </h4>
                            <p class="text-2xl font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                        </div>
                        <div class="overflow-y-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jml</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($penjualanData as $item)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $item->nama_obat }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center">{{ $item->jumlah }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-4 text-gray-500">Tidak ada penjualan pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">
                            {{ $laporanPembelianTitle }}
                        </h3>
                        <form id="form-filter-pembelian" class="mb-4 p-4 bg-gray-100 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="pembelian_mulai" class="text-sm font-medium">Dari Tanggal</label>
                                    <input type="date" name="pembelian_mulai" id="pembelian_mulai" value="{{ $filterPembelianMulai }}" class="w-full border-gray-300 rounded-md shadow-sm ">
                                </div>
                                <div>
                                    <label for="pembelian_akhir" class="text-sm font-medium">Sampai Tanggal</label>
                                    <input type="date" name="pembelian_akhir" id="pembelian_akhir" value="{{ $filterPembelianAkhir }}" class="w-full border-gray-300 rounded-md shadow-sm ">
                                </div>
                                <div class="self-end">
                                    <button type="button" class="btn-filter w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-md mt-6">Filter</button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="bg-red-100 border border-red-300 p-4  mb-4">
                            <h4 class="font-semibold text-red-800">Total Pengeluaran</h4>
                            <p class="text-2xl font-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                        </div>

                        <div class="overflow-y-auto max-h-96">
                             <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Obat</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jml</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($pembelianData as $item)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d-m-y') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $item->nama_obat }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center">{{ $item->jumlah }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status == 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $item->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center py-4 text-gray-500">Tidak ada pembelian pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-filter').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const form = this.closest('form');
                    const params = new URLSearchParams();
                    const currentParams = new URLSearchParams(window.location.search);
                    const otherFormId = form.id === 'form-filter-penjualan' ? 'form-filter-pembelian' : 'form-filter-penjualan';
                    const otherFormInputs = document.querySelectorAll(`#${otherFormId} [name]`);
                    otherFormInputs.forEach(input => {
                        if (currentParams.has(input.name)) {
                            params.append(input.name, currentParams.get(input.name));
                        }
                    });
                    new FormData(form).forEach((value, key) => {
                        if (value) {
                            params.set(key, value);
                        }
                    });
                    window.location.href = `{{ route('dashboard') }}?${params.toString()}`;
                });
            });
            const tanggalInput = document.getElementById('tanggal');
            const bulanSelect = document.getElementById('bulan');
            const tahunSelect = document.getElementById('tahun');
            if (bulanSelect && tahunSelect && tanggalInput) {
                const resetTanggal = () => tanggalInput.value = '';
                bulanSelect.addEventListener('change', resetTanggal);
                tahunSelect.addEventListener('change', resetTanggal);
            }
        });
    </script>
    @endpush
</x-app-layout>