<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                {{-- [MODIFIED] Menggunakan Alpine.js untuk menyembunyikan/menampilkan form --}}
                <div x-data="{ open: {{ $user->exists || $errors->any() ? 'true' : 'false' }} }">
                    {{-- Tombol untuk menampilkan form tambah pengguna --}}
                    <div class="mb-4" x-show="!open">
                        <x-button @click="open = true">
                            Tambah Pengguna Baru
                        </x-button>
                    </div>

                    {{-- Formulir Dinamis untuk Tambah & Edit Pengguna --}}
                    <div class="mb-8 p-4 bg-gray-50 border border-gray-200 rounded-lg" x-show="open" x-transition>
                        <h3 class="text-lg font-semibold mb-4">
                            @if ($user->exists)
                                Edit Data Pengguna
                            @else
                                Tambah Pengguna Baru
                            @endif
                        </h3>

                        <x-validation-errors class="mb-4" />

                        <form action="{{ $user->exists ? route('users.update', $user->id) : route('users.store') }}" method="POST">
                            @csrf
                            @if ($user->exists)
                                @method('PUT')
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Kolom Kiri --}}
                                <div>
                                    <div>
                                        <x-label for="name" value="Nama Pengguna" />
                                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="email" value="Alamat Email" />
                                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="role" value="Role / Peran" />
                                        <select name="role" id="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="">-- Pilih Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                                    {{ Str::title($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Kolom Kanan --}}
                                <div>
                                    <div>
                                        <x-label for="password" value="Password" />
                                        {{-- [FIXED] Mengganti x-input dengan input HTML standar untuk memastikan bisa diklik --}}
                                        <input id="password" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="password" name="password" autocomplete="new-password" @if(!$user->exists) required @endif>
                                        @if ($user->exists)
                                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <x-label for="password_confirmation" value="Konfirmasi Password" />
                                        {{-- [FIXED] Mengganti x-input dengan input HTML standar untuk memastikan bisa diklik --}}
                                        <input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="password" name="password_confirmation" autocomplete="new-password" @if(!$user->exists) required @endif>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end mt-6">
                                @if ($user->exists)
                                    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                                @else
                                    <button type="button" @click="open = false" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</button>
                                @endif
                                <x-button>
                                    {{ $user->exists ? 'Update Pengguna' : 'Simpan Pengguna' }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabel Daftar Pengguna --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(!$item->roles->isEmpty())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ Str::title($item->roles->first()->name) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('users.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <form action="{{ route('users.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pengguna lain.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
