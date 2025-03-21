<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('penjualan_obat')->insert([
            [
                'nama_obat' => 'Paracetamol 500mg',
                'harga' => 5000,
                'tanggal_keluar' => '2024-03-10',
                'jumlah_pembelian' => 2,
                'total_harga' => 10000
            ],
            [
                'nama_obat' => 'Amoxicillin 250mg',
                'harga' => 7000,
                'tanggal_keluar' => '2024-03-11',
                'jumlah_pembelian' => 1,
                'total_harga' => 7000
            ],
            [
                'nama_obat' => 'Ibuprofen 200mg',
                'harga' => 8000,
                'tanggal_keluar' => '2024-03-12',
                'jumlah_pembelian' => 3,
                'total_harga' => 24000
            ],
            [
                'nama_obat' => 'Cetirizine 10mg',
                'harga' => 6000,
                'tanggal_keluar' => '2024-03-13',
                'jumlah_pembelian' => 2,
                'total_harga' => 12000
            ],
            [
                'nama_obat' => 'Lansoprazole 30mg',
                'harga' => 10000,
                'tanggal_keluar' => '2024-03-14',
                'jumlah_pembelian' => 1,
                'total_harga' => 10000
            ],
            [
                'nama_obat' => 'Metformin 500mg',
                'harga' => 5000,
                'tanggal_keluar' => '2024-03-15',
                'jumlah_pembelian' => 4,
                'total_harga' => 20000
            ],
            [
                'nama_obat' => 'Simvastatin 20mg',
                'harga' => 9000,
                'tanggal_keluar' => '2024-03-16',
                'jumlah_pembelian' => 1,
                'total_harga' => 9000
            ],
            [
                'nama_obat' => 'Ciprofloxacin 500mg',
                'harga' => 11000,
                'tanggal_keluar' => '2024-03-17',
                'jumlah_pembelian' => 2,
                'total_harga' => 22000
            ],
            [
                'nama_obat' => 'Ranitidine 150mg',
                'harga' => 7000,
                'tanggal_keluar' => '2024-03-18',
                'jumlah_pembelian' => 3,
                'total_harga' => 21000
            ],
            [
                'nama_obat' => 'Dexamethasone 0.5mg',
                'harga' => 4000,
                'tanggal_keluar' => '2024-03-19',
                'jumlah_pembelian' => 5,
                'total_harga' => 20000
            ],
        ]);
    }
}
