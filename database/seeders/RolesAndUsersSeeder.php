<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache peran dan izin
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Semua Peran
        $role_kepala = Role::firstOrCreate(['name' => 'kepala apotek']);
        $role_admin = Role::firstOrCreate(['name' => 'admin']);
        $role_apoteker = Role::firstOrCreate(['name' => 'apoteker']);
        
        // 2. Buat Pengguna Kepala Apotek
        $kepala = User::firstOrCreate(
            ['email' => 'kepala@apotek.com'],
            [
                'name' => 'Kepala Apotek',
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang aman
            ]
        );
        // Tetapkan peran
        $kepala->assignRole($role_kepala);

        // 3. Buat Pengguna Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@apotek.com'],
            [
                'name' => 'Admin Apotek',
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang aman
            ]
        );
        // Tetapkan peran
        $admin->assignRole($role_admin);

        // 4. Buat Pengguna Apoteker
        $apoteker = User::firstOrCreate(
            ['email' => 'apoteker@apotek.com'],
            [
                'name' => 'Apoteker',
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang aman
            ]
        );
        // Tetapkan peran
        $apoteker->assignRole($role_apoteker);
    }
}