<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class data_pembelian extends Seeder
{
    public function run(): void
    {
        $dataPembelian = [
            [
                'nama_obat' => 'Paracetamol 500mg',
                'kode_obat' => 'PCT500',
                'harga_satuan' => 4000,
                'harga_box' => 380000,
                'qty' => 100,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier A',
                'tanggal' => now()->subDays(10), // tanggal pembelian 10 hari yang lalu
            ],
            [
                'nama_obat' => 'Amoxicillin 250mg',
                'kode_obat' => 'AMX250',
                'harga_satuan' => 7000,
                'harga_box' => 650000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier B',
                'tanggal' => now()->subDays(5), // tanggal pembelian 5 hari yang lalu
            ],
            [
                'nama_obat' => 'Cetirizine 10mg',
                'kode_obat' => 'CTZ10',
                'harga_satuan' => 5000,
                'harga_box' => 480000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier C',
                'tanggal' => now()->subDays(3), // tanggal pembelian 3 hari yang lalu
            ],
            [
                'nama_obat' => 'Ibuprofen 200mg',
                'kode_obat' => 'IBP200',
                'harga_satuan' => 6000,
                'harga_box' => 570000,
               'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier D',
                'tanggal' => now()->subDays(1), // tanggal pembelian 1 hari yang lalu
            ],
            [
                'nama_obat' => 'Metformin 500mg',
                'kode_obat' => 'MTF500',
                'harga_satuan' => 8000,
                'harga_box' => 750000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier E',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
            [
                'nama_obat' => 'Ranitidine 150mg',
                'kode_obat' => 'RNT150',
                'harga_satuan' => 7500,
                'harga_box' => 720000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier F',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
            [
                'nama_obat' => 'Omeprazole 20mg',
                'kode_obat' => 'OMP20',
                'harga_satuan' => 9000,
                'harga_box' => 850000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier G',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
            [
                'nama_obat' => 'Salbutamol 2mg',
                'kode_obat' => 'SLB2',
                'harga_satuan' => 4500,
                'harga_box' => 420000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier H',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
            [
                'nama_obat' => 'Vitamin C 500mg',
                'kode_obat' => 'VTC500',
                'harga_satuan' => 3000,
                'harga_box' => 280000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier I',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
            [
                'nama_obat' => 'Loperamide 2mg',
                'kode_obat' => 'LPR2',
                'harga_satuan' => 5500,
                'harga_box' => 520000,
                'qty' => 55,
                'created_at' => now(),
                'updated_at' => now(),
                'supplier' => 'Supplier J',
                'tanggal' => now(), // tanggal pembelian hari ini
            ],
        ];

        DB::table('data_pembelian')->insert($dataPembelian);
    }
}
