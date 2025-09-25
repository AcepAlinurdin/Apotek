<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Data Pasien') }}
        </h2>
    </x-slot>

    <!-- State management for the form and list view -->
    <div class="py-12" x-data="{
        mode: 'list',
        patient: {},
        submitForm(event) {
            const form = event.target;
            const formData = new FormData(form);
            const action = form.getAttribute('action');

            if (this.mode === 'edit') {
                formData.append('_method', 'PUT');
            }

            fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => {
                // ================== PERBAIKAN NOTIFIKASI ==================
                // Jika controller berhasil dan melakukan redirect, langsung ikuti redirect tersebut.
                // Pesan sukses spesifik akan ditampilkan oleh Blade di halaman yang dimuat ulang.
                if (response.redirected) {
                    window.location.href = response.url;
                    return; // Hentikan eksekusi
                }
                // Fallback jika berhasil tapi tidak ada redirect
                if (response.ok) {
                    window.location.reload();
                    return;
                }
                // ==========================================================
                
                const responseClone = response.clone();
                return response.json().catch(() => {
                    return responseClone.text().then(text => {
                        throw new Error(`Server merespons dengan non-JSON (Status: ${response.status}). Konten: ${text.substring(0, 150)}...`);
                    });
                });
            })
            .then(data => {
                if (data && data.message) {
                    alert('Gagal menyimpan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Form Submit Error:', error);
                alert('Terjadi kesalahan teknis. Silakan periksa console browser untuk detail.');
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Button to toggle form visibility -->
            <div class="mb-4 flex justify-end">
                <button @click="if (mode === 'list') { mode = 'create'; patient = {}; } else { mode = 'list'; patient = {}; }" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <span x-show="mode === 'list'">
                        <i class="fas fa-plus mr-2"></i>Tambah Pasien Baru
                    </span>
                    <span x-show="mode !== 'list'">
                        <i class="fas fa-times mr-2"></i>Tutup Form
                    </span>
                </button>
            </div>

            <!-- Form for creating and editing patients -->
            <div x-show="mode !== 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8 mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800" x-text="mode === 'create' ? 'Formulir Tambah Pasien' : 'Formulir Edit Pasien'"></h3>
                
                <form :action="mode === 'create' ? '{{ route('pasien.store') }}' : '/pasien/' + patient.id" method="POST" @submit.prevent.stop="submitForm($event)">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_lengkap" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.nama_lengkap" required autofocus>
                            @error('nama_lengkap')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label for="tanggal_lahir" class="block font-medium text-sm text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.tanggal_lahir">
                            @error('tanggal_lahir')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label for="jenis_kelamin" class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label for="nomor_telepon" class="block font-medium text-sm text-gray-700">Nomor Telepon</label>
                            <input type="tel" name="nomor_telepon" id="nomor_telepon" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.nomor_telepon">
                            @error('nomor_telepon')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Riwayat Penyakit --}}
                        <div>
                            <label for="riwayat_penyakit" class="block font-medium text-sm text-gray-700">Riwayat Penyakit</label>
                            <input type="text" name="riwayat_penyakit" id="riwayat_penyakit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.riwayat_penyakit">
                            @error('riwayat_penyakit')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Catatan Alergi --}}
                        <div>
                            <label for="catatan_alergi" class="block font-medium text-sm text-gray-700">Alergi</label>
                            <input type="text" name="catatan_alergi" id="catatan_alergi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.catatan_alergi">
                            @error('catatan_alergi')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label for="alamat" class="block font-medium text-sm text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" x-model="patient.alamat"></textarea>
                            @error('alamat')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                    </div>
                    {{-- Tombol Aksi Form --}}
                    <div class="flex items-center justify-end mt-6">
                        <button type="button" @click="mode = 'list'; patient = {}" class="text-gray-600 hover:text-gray-900 mr-4">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <span x-text="mode === 'create' ? 'Simpan Pasien' : 'Update Pasien'"></span>
                        </button>
                    </div>
                </form>
            </div>


            {{-- Tabel Daftar Pasien --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Pasien</h3>
                    
                    {{-- Form Pencarian --}}
                    <div class="mb-4">
                        <form action="{{ route('pasien.index') }}" method="GET" class="flex items-center space-x-2">
                            <input type="text" name="search" placeholder="Cari nama atau no. telepon..."
                                   class="w-full md:w-1/3 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ request('search') }}">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                Cari
                            </button>
                            <a href="{{ route('pasien.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Reset
                            </a>
                        </form>
                    </div>

                    {{-- Pesan Sukses --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Riwayat penyakit</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alergi</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pasiens as $pasien)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pasien->nama_lengkap }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pasien->nomor_telepon ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pasien->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pasien->riwayat_penyakit ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pasien->catatan_alergi ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button type="button" @click="mode = 'edit'; patient = {{ $pasien->toJson() }}; window.scrollTo({ top: 0, behavior: 'smooth' })" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                        <form action="{{ route('pasien.destroy', $pasien) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus data pasien ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        Tidak ada data pasien yang cocok dengan pencarian Anda.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginasi --}}
                    <div class="mt-4">
                        {{ $pasiens->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

