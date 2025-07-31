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
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $supplier->exists ? 'Edit Data Supplier' : 'Tambah Supplier Baru' }}
                    </h3>

                    <x-validation-errors class="mb-4" />

                    <form action="{{ $supplier->exists ? route('suppliers.update', $supplier->id) : route('suppliers.store') }}" method="POST">
                        @csrf
                        @if ($supplier->exists)
                            @method('PUT')
                        @endif

                        <div class="space-y-4">
                            <div>
                                <x-label for="nama_supplier" value="Nama Supplier" />
                                <x-input id="nama_supplier" class="block mt-1 w-full" type="text" name="nama_supplier" :value="old('nama_supplier', $supplier->nama_supplier)" required autofocus />
                            </div>
                            <div>
                                <x-label for="alamat" value="Alamat (Opsional)" />
                                <textarea id="alamat" name="alamat" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $supplier->alamat) }}</textarea>
                            </div>
                            <div>
                                <x-label for="telepon" value="Nomor Telepon (Opsional)" />
                                <x-input id="telepon" class="block mt-1 w-full" type="text" name="telepon" :value="old('telepon', $supplier->telepon)" />
                            </div>
                            <div class="flex items-center justify-end">
                                @if ($supplier->exists)
                                    <a href="{{ route('suppliers.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($suppliers as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nama_supplier }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($item->alamat, 50) ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->telepon ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('suppliers.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data supplier.</td>
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

    {{-- Alert Biasa --}}
    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif
</x-app-layout>
