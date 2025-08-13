<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan peran (roles) sudah ada sebelum membuat user.
        // Jika belum, Anda harus menjalankan RoleSeeder terlebih dahulu.
        $adminRole = Role::firstWhere('name', 'admin');
        $apotekerRole = Role::firstWhere('name', 'apoteker');
        $kepalaApotekRole = Role::firstWhere('name', 'kepala apotek');

        // Buat atau perbarui akun untuk peran 'admin'
        $admin = User::firstOrCreate(
            ['email' => 'admin@apotek.com'],
            [
                'name' => 'Admin Apotek',
                'password' => Hash::make('password'),
            ]
        );
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        // Buat atau perbarui akun untuk peran 'apoteker'
        $apoteker = User::firstOrCreate(
            ['email' => 'apoteker@apotek.com'],
            [
                'name' => 'Apoteker Apotek',
                'password' => Hash::make('password'),
            ]
        );
        if ($apotekerRole) {
            $apoteker->assignRole($apotekerRole);
        }
        
        // Buat atau perbarui akun untuk peran 'kepala apotek'
        $kepalaApotek = User::firstOrCreate(
            ['email' => 'kepala@apotek.com'],
            [
                'name' => 'Kepala Apotek',
                'password' => Hash::make('password'),
            ]
        );
        if ($kepalaApotekRole) {
            $kepalaApotek->assignRole($kepalaApotekRole);
        }
    }
}
