<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat peran jika belum ada
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'apoteker']);
        Role::firstOrCreate(['name' => 'kepala apotek']);
    }
}

