<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna dan form untuk tambah/edit.
     */
    public function index()
    {
        // Panggil method 'edit' dengan user baru agar formnya kosong
        return $this->edit(new User());
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
        $requestedRole = $request->input('role');

        // Keamanan di backend: Gunakan Gate untuk otorisasi
        Gate::authorize('create-user-with-role', $requestedRole);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Gunakan method dari Spatie untuk menetapkan role
        $user->assignRole($requestedRole);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit di halaman daftar pengguna.
     * Ini juga akan menangani tampilan untuk 'create'.
     */
    public function edit(User $user)
    {
        $users = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        
        // --- LOGIKA PENYARINGAN ROLE ---
        $allRoles = Role::all();
        $allowedRoles = [];

        // Loop melalui semua role dan periksa izinnya menggunakan Gate
        foreach ($allRoles as $role) {
            if (Gate::allows('create-user-with-role', $role->name)) {
                $allowedRoles[] = $role;
            }
        }
        
        return view('users.index', [
            'users' => $users,
            'user' => $user, // Ini bisa user baru (kosong) atau user yang diedit
            'roles' => $allowedRoles // Kirim role yang sudah disaring
        ]);
    }

    /**
     * Memperbarui data pengguna di dalam database.
     */
    public function update(Request $request, User $user)
    {
        $requestedRole = $request->input('role');

        // Keamanan di backend: Gunakan Gate untuk otorisasi
        // Asumsi aturan untuk update sama dengan create
        Gate::authorize('create-user-with-role', $requestedRole);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Gunakan method dari Spatie untuk sinkronisasi role
        $user->syncRoles($requestedRole);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}