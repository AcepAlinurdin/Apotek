<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Obat;
use App\Models\Supplier;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Roles untuk Spatie
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'kasir']);
        Role::firstOrCreate(['name' => 'kepala apotek']);

        // 2. Membuat User dan memberikan peran
        $admin = User::firstOrCreate(
            ['email' => 'admin@apotek.com'],
            [
                'name' => 'Admin Apotek',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');
        
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@apotek.com'],
            [
                'name' => 'Kasir Apotek',
                'password' => Hash::make('kasir123'),
            ]
        );
        $kasir->assignRole('kasir');

        $kepalaApotek = User::firstOrCreate(
            ['email' => 'kepala@apotek.com'],
            [
                'name' => 'Kepala Apotek',
                'password' => Hash::make('password'),
            ]
        );
        $kepalaApotek->assignRole('kepala apotek');


        // 3. Menambahkan data dummy untuk Supplier
        Supplier::firstOrCreate(['nama_supplier' => 'PT. Kimia Farma']);
        Supplier::firstOrCreate(['nama_supplier' => 'PT. Kalbe Farma']);
        Supplier::firstOrCreate(['nama_supplier' => 'PT. Phapros']);
        
        // Mengambil semua supplier untuk dihubungkan dengan obat
        $suppliers = Supplier::all();


        // 4. Menambahkan data dummy untuk Obat
        Obat::firstOrCreate(['nama_obat' => 'Paracetamol'], [
            'satuan' => 'strip',
            'harga_pcs' => 2000.00,
            'harga_box' => 20000.00,
            'supplier_id' => $suppliers->firstWhere('nama_supplier', 'PT. Kimia Farma')->id,
        ]);
        
        Obat::firstOrCreate(['nama_obat' => 'Amoxicillin'], [
            'satuan' => 'strip',
            'harga_pcs' => 5000.00,
            'harga_box' => 50000.00,
            'supplier_id' => $suppliers->firstWhere('nama_supplier', 'PT. Kimia Farma')->id,
        ]);

        Obat::firstOrCreate(['nama_obat' => 'Amoxicillin'], [
            'satuan' => 'box',
            'harga_pcs' => 500.00,
            'harga_box' => 50000.00,
            'supplier_id' => $suppliers->firstWhere('nama_supplier', 'PT. Kalbe Farma')->id,
        ]);
        
        Obat::firstOrCreate(['nama_obat' => 'Ibuprofen'], [
            'satuan' => 'strip',
            'harga_pcs' => 3500.00,
            'harga_box' => 35000.00,
            'supplier_id' => $suppliers->firstWhere('nama_supplier', 'PT. Phapros')->id,
        ]);
    }
}
