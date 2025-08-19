<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                {{-- Formulir Tambah/Edit --}}
                <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    {{-- [REVISED] Menambahkan class flex untuk mengatur layout --}}
                    <h3 class="flex justify-between items-center text-lg font-semibold mb-4">

                        {{-- Judul di sebelah kiri --}}
                        <span>
                            {{ $supplier->exists ? 'Edit Data Supplier' : 'Tambah Supplier Baru' }}
                        </span>

                        {{-- Link di sebelah kanan --}}
                        <a href="{{ route('obat.master.index') }}" class="text-sm text-blue-600 hover:underline">
                            &larr; Kembali ke Master Data
                        </a>

                    </h3>

                    <x-validation-errors class="mb-4" />

                    <form
                        action="{{ $supplier->exists ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}"
                        method="POST">
                        @csrf
                        @if ($supplier->exists)
                        @method('PUT')
                        @endif

                        <div class="space-y-4">
                            <div>
                                <x-label for="nama_supplier" value="Nama Supplier" />
                                <x-input id="nama_supplier" class="block mt-1 w-full" type="text" name="nama_supplier"
                                    :value="old('nama_supplier', $supplier->nama_supplier)" required autofocus />
                            </div>
                            <div>
                                <x-label for="alamat" value="Alamat (Opsional)" />
                                <textarea id="alamat" name="alamat" rows="3"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $supplier->alamat) }}</textarea>
                            </div>
                            <div>
                                <x-label for="telepon" value="Nomor Telepon (Opsional)" />
                                <x-input id="telepon" class="block mt-1 w-full" type="text" name="telepon"
                                    :value="old('telepon', $supplier->telepon)" />
                            </div>
                            <div class="flex items-center justify-end">
                                @if ($supplier->exists)
                                <a href="{{ route('suppliers.index') }}"
                                    class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                                @endif
                                <x-button>
                                    {{ $supplier->exists ? 'Update' : 'Simpan' }}
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tabel Daftar Supplier --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                    Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($suppliers as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->nama_supplier }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($item->alamat, 50) ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->telepon ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    {{-- ✅ TOMBOL BARU: Tombol 'Lihat' untuk menampilkan detail --}}
                                    <button class="show-btn text-blue-600 hover:text-blue-900 mr-4"
                                        data-id="{{ $item->id }}">Lihat</button>
                                    <a href="{{ route('suppliers.edit', $item->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $item->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data
                                    supplier.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.show-btn').forEach(button => {
            button.addEventListener('click', function () {
                const supplierId = this.dataset.id;

                // Ambil data dari server
                fetch(`/suppliers/${supplierId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil data supplier.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        let obatListHtml = `
                            <div class="overflow-y-auto max-h-5 mt-4">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">Nama Obat</th>
                                            <th scope="col" class="px-6 py-3 text-center">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        if (data.obats && data.obats.length > 0) {
                            data.obats.forEach(obat => {
                                obatListHtml += `
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${obat.nama_obat}</td>
                                        <td class="px-6 py-4 text-center">${obat.stok}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            obatListHtml += `
                                <tr class="bg-white">
                                    <td colspan="2" class="px-6 py-4 text-center text-gray-500">Tidak ada obat yang terdaftar.</td>
                                </tr>
                            `;
                        }
                        obatListHtml += `</tbody></table></div>`;


                        Swal.fire({
                            title: `<strong>DAFTAR OBAT ${data.nama_supplier}</strong>`,
                            icon: 'info',
                            html: `
                                <div class="text-left">
                                    <p class="mb-2"><strong>Alamat:</strong> ${data.alamat || 'N/A'}</p>
                                    <p class="mb-4"><strong>Telepon:</strong> ${data.telepon || 'N/A'}</p>
                                    <h4 class="font-bold mt-4">Daftar Obat:</h4>
                                    ${obatListHtml}
                                </div>
                            `,
                            showCloseButton: true,
                            confirmButtonText: 'Tutup'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message,
                        });
                    });
            });
        });
    });

</script>
