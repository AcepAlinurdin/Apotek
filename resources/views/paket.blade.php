<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Paket Obat') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="paketManager({{ $obats }})">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-end">
                <button @click="openForm('create')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Paket Baru
                </button>
            </div>

            <!-- Modal Form for Create/Edit -->
            <div x-show="showModal" @keydown.escape.window="closeForm()" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeForm()" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                        <form :action="formAction" method="POST">
                            @csrf
                            <template x-if="mode === 'edit'">
                                @method('PUT')
                            </template>
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="mode === 'create' ? 'Tambah Paket Baru' : 'Edit Paket'"></h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="nama_paket" class="block text-sm font-medium text-gray-700">Nama Paket</label>
                                        <input type="text" name="nama_paket" x-model="paket.nama_paket" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Cari & Tambah Obat</label>
                                        <input type="text" x-model="searchTerm" placeholder="Ketik untuk mencari obat..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <div class="mt-2 max-h-40 overflow-y-auto border rounded-md">
                                            <template x-for="obat in filteredObats" :key="obat.id">
                                                <div class="flex justify-between items-center p-2 hover:bg-gray-100">
                                                    <span x-text="obat.nama_obat"></span>
                                                    <button type="button" @click="addObatToPaket(obat)" class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">Tambah</button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Obat dalam Paket</label>
                                        <div class="mt-2 border rounded-md p-2 space-y-2 min-h-[5rem]">
                                            <template x-if="paket.obats.length === 0">
                                                <p class="text-center text-gray-500 text-sm">Belum ada obat ditambahkan.</p>
                                            </template>
                                            <template x-for="(selected, index) in paket.obats" :key="selected.id">
                                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                                    <span x-text="selected.nama_obat"></span>
                                                    <div class="flex items-center space-x-2">
                                                        <label class="text-sm">Jumlah:</label>
                                                        <input type="number" :name="`obats[${index}][jumlah]`" x-model="selected.jumlah" min="1" class="w-16 text-center border-gray-300 rounded-md shadow-sm">
                                                        <input type="hidden" :name="`obats[${index}][id]`" :value="selected.id">
                                                        <button type="button" @click="removeObatFromPaket(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm" x-text="mode === 'create' ? 'Simpan' : 'Update'"></button>
                                <button type="button" @click="closeForm()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Paket -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Paket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Isi Paket</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pakets as $paket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $paket->nama_paket }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <ul class="list-disc list-inside">
                                            @foreach($paket->detailPaketObats as $detail)
                                                <li>{{ $detail->obat->nama_obat ?? 'Obat Dihapus' }} ({{ $detail->jumlah }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button @click="openForm('edit', {{ $paket->toJson() }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <form action="{{ route('paket-obat.destroy', $paket) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus paket ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada paket obat yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $pakets->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function paketManager(allObats) {
            return {
                showModal: false,
                mode: 'create',
                paket: {},
                formAction: '{{ route("paket-obat.store") }}',
                allObats: allObats,
                searchTerm: '',
                
                get filteredObats() {
                    if (this.searchTerm === '') {
                        return [];
                    }
                    return this.allObats.filter(obat => {
                        return obat.nama_obat.toLowerCase().includes(this.searchTerm.toLowerCase());
                    }).slice(0, 5); // Tampilkan 5 hasil teratas
                },

                openForm(mode, paket = null) {
                    this.mode = mode;
                    if (mode === 'create') {
                        this.paket = { nama_paket: '', obats: [] };
                        this.formAction = '{{ route("paket-obat.store") }}';
                    } else {
                        const detailPaket = paket.detail_paket_obats.map(d => ({
                            id: d.obat.id,
                            nama_obat: d.obat.nama_obat,
                            jumlah: d.jumlah
                        }));
                        this.paket = { id: paket.id, nama_paket: paket.nama_paket, obats: detailPaket };
                        this.formAction = `/paket-obat/${paket.id}`;
                    }
                    this.showModal = true;
                },

                closeForm() {
                    this.showModal = false;
                    this.searchTerm = '';
                },

                addObatToPaket(obat) {
                    // Cek jika obat sudah ada di paket
                    const existing = this.paket.obats.find(o => o.id === obat.id);
                    if (existing) {
                        existing.jumlah++;
                    } else {
                        this.paket.obats.push({ id: obat.id, nama_obat: obat.nama_obat, jumlah: 1 });
                    }
                    this.searchTerm = '';
                },

                removeObatFromPaket(index) {
                    this.paket.obats.splice(index, 1);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
```

### **Langkah Selanjutnya (Penting):**

1.  **Buat Controller**: Jalankan `php artisan make:controller PaketObatController` di terminal Anda, lalu salin-tempel kode dari file `PaketObatController.php` di atas.
2.  **Buat View**: Buat folder baru `paket` di dalam `resources/views`. Di dalamnya, buat file `index.blade.php` dan salin-tempel kode dari `Halaman Manajemen Paket Obat`.
3.  **Tambahkan Route**: Buka file `routes/web.php` dan tambahkan baris ini di dalam grup *middleware auth* Anda.
    ```php
    use App\Http\Controllers\PaketObatController;

    Route::resource('paket-obat', PaketObatController::class)->middleware('auth');
    ```
4.  **Tambahkan Model**: Anda juga perlu membuat dua model baru.
    * Jalankan `php artisan make:model PaketObat -m` (sudah Anda lakukan).
    * Jalankan `php artisan make:model DetailPaketObat -m` (sudah Anda lakukan).
    * Pastikan model `PaketObat.php` memiliki relasi:
        ```php
        public function detailPaketObats() {
            return $this->hasMany(DetailPaketObat::class);
        }
        ```
    * Dan `DetailPaketObat.php` memiliki relasi:
        ```php
        public function obat() {
            return $this->belongsTo(Obat::class);
        }
        ```
5.  **Tambahkan Link di Navigasi**: Terakhir, buka `resources/views/navigation-menu.blade.php` dan tambahkan link ke halaman baru ini agar mudah diakses, misalnya:
    ```html
    <x-nav-link href="{{ route('paket-obat.index') }}" :active="request()->routeIs('paket-obat.*')">
        {{ __('Manajemen Paket') }}
    </x-nav-link>
    
