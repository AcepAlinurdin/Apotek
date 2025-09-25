<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pasien Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
                
                {{-- Form untuk menambah pasien baru --}}
                <form action="{{ route('pasien.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_lengkap" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('nama_lengkap') }}" required autofocus>
                            @error('nama_lengkap')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label for="tanggal_lahir" class="block font-medium text-sm text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('tanggal_lahir') }}">
                             @error('tanggal_lahir')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label for="jenis_kelamin" class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                             @error('jenis_kelamin')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label for="nomor_telepon" class="block font-medium text-sm text-gray-700">Nomor Telepon</label>
                            <input type="tel" name="nomor_telepon" id="nomor_telepon" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('nomor_telepon') }}">
                             @error('nomor_telepon')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Nomor KTP (Opsional) --}}
                        <div class="md:col-span-2">
                            <label for="nomor_ktp" class="block font-medium text-sm text-gray-700">Nomor KTP (Opsional)</label>
                            <input type="text" name="nomor_ktp" id="nomor_ktp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('nomor_ktp') }}">
                             @error('nomor_ktp')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label for="alamat" class="block font-medium text-sm text-gray-700">Alamat</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('alamat') }}</textarea>
                             @error('alamat')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan Alergi --}}
                        <div class="md:col-span-2">
                            <label for="catatan_alergi" class="block font-medium text-sm text-gray-700">Catatan Alergi Obat (Jika ada)</label>
                            <textarea name="catatan_alergi" id="catatan_alergi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('catatan_alergi') }}</textarea>
                             @error('catatan_alergi')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('pasien.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Simpan Pasien
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>