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
    public function index()
    {
   
        return $this->edit(new User());
    }

 
    public function store(Request $request)
    {
        $requestedRole = $request->input('role');

    
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

        $user->assignRole($requestedRole);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }


    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $users = User::where('id', '!=', auth()->id())->latest()->paginate(10);
        
        $allRoles = Role::all();
        $allowedRoles = [];

        
        foreach ($allRoles as $role) {
            if (Gate::allows('create-user-with-role', $role->name)) {
                $allowedRoles[] = $role;
            }
        }
        
        return view('users.index', [
            'users' => $users,
            'user' => $user, 
            'roles' => $allowedRoles 
        ]);
    }

  
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $requestedRole = $request->input('role');

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

      
        $user->syncRoles($requestedRole);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
{
   
    $this->authorize('delete', $user);

    $user->delete();
    return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
}
}