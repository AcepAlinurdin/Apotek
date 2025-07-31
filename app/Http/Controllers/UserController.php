<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna beserta formulir.
     */
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        $roles = Role::all();
        
        // Mengirim objek User kosong untuk mode 'create'
        return view('users.index', [
            'users' => $users,
            'user' => new User(),
            'roles' => $roles
        ]);
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
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

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit di halaman daftar pengguna.
     */
    public function edit(User $user)
    {
        $users = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        $roles = Role::all();
        
        // Mengirim data user yang akan diedit ke view yang sama
        return view('users.index', compact('users', 'user', 'roles'));
    }

    /**
     * Memperbarui data pengguna di dalam database.
     */
    public function update(Request $request, User $user)
    {
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

        $user->syncRoles($request->role);

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
