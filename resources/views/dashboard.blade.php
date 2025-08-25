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
                {{-- Bagian Laporan Penjualan & Pembelian --}}
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
                        <div class="bg-green-100 border border-green-300 p-4 mb-4 rounded-lg">
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
                    {{-- Kolom 2: Laporan Pembelian Dinamis --}}
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
                        <div class="bg-red-100 border border-red-300 p-4 mb-4 rounded-lg">
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

                <!-- TABEL BARU: PEMBELIAN BELUM LUNAS -->
                <div class="p-6 sm:px-20 bg-white">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Daftar Pembelian Belum Lunas</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead class="bg-gray-100">
                                <tr class="text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Tanggal</th>
                                    <th class="py-3 px-6 text-left">Supplier</th>
                                    <th class="py-3 px-6 text-right">Total Harga</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-belum-lunas" class="text-gray-700 text-sm">
                                @forelse($pembelianBelumLunas as $pembelian)
                                <tr id="row-pembelian-{{ $pembelian->id }}" class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('d M Y') }}</td>
                                    <td class="py-3 px-6 text-left font-medium">{{ $pembelian->supplier->nama_supplier ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-right">Rp {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-semibold">{{ $pembelian->status }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <button class="btn-lunas bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600 text-xs font-bold" data-id="{{ $pembelian->id }}">
                                            LUNASI
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center p-6 text-gray-500">Tidak ada data pembelian yang belum lunas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            $('#tabel-belum-lunas').on('click', '.btn-lunas', function() {
                const pembelianId = $(this).data('id');
                const url = `/pembelian/${pembelianId}/lunas`;

                Swal.fire({
                    title: 'Konfirmasi Pelunasan',
                    text: "Anda yakin ingin mengubah status pembelian ini menjadi Lunas?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lunasi!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'PATCH',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    $(`#row-pembelian-${pembelianId}`).fadeOut(500, function() {
                                        $(this).remove();
                                        if ($('#tabel-belum-lunas tr').length === 1) { // Hanya ada header tr
                                            $('#tabel-belum-lunas').append('<tr><td colspan="5" class="text-center p-6 text-gray-500">Tidak ada data pembelian yang belum lunas.</td></tr>');
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON.message || 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>