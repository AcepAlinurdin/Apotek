<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataObatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('data_obat')->truncate();

        $data = [
            ['nama_obat' => 'Paracetamol 500mg', 'harga' => 4000, 'stok' => 50],
            ['nama_obat' => 'Amoxicillin 250mg', 'harga' => 7000, 'stok' => 30],
            ['nama_obat' => 'Cetirizine 10mg', 'harga' => 5000, 'stok' => 40],
            ['nama_obat' => 'Ibuprofen 200mg', 'harga' => 6000, 'stok' => 25],
            ['nama_obat' => 'Metformin 500mg', 'harga' => 8000, 'stok' => 20],
            ['nama_obat' => 'Ranitidine 150mg', 'harga' => 7500, 'stok' => 35],
            ['nama_obat' => 'Omeprazole 20mg', 'harga' => 9000, 'stok' => 15],
            ['nama_obat' => 'Salbutamol 2mg', 'harga' => 4500, 'stok' => 45],
            ['nama_obat' => 'Vitamin C 500mg', 'harga' => 3000, 'stok' => 50],
            ['nama_obat' => 'Loperamide 2mg', 'harga' => 5500, 'stok' => 30],
            ['nama_obat' => 'Simvastatin 10mg', 'harga' => 12000, 'stok' => 10],
            ['nama_obat' => 'Dexamethasone 0.5mg', 'harga' => 4000, 'stok' => 25],
            ['nama_obat' => 'Chlorpheniramine 4mg', 'harga' => 2500, 'stok' => 50],
            ['nama_obat' => 'Aspirin 100mg', 'harga' => 6500, 'stok' => 20],
            ['nama_obat' => 'Ciprofloxacin 500mg', 'harga' => 10000, 'stok' => 15],
            ['nama_obat' => 'Furosemide 40mg', 'harga' => 8500, 'stok' => 10],
            ['nama_obat' => 'Losartan 50mg', 'harga' => 9500, 'stok' => 30],
            ['nama_obat' => 'Amlodipine 5mg', 'harga' => 11000, 'stok' => 25],
            ['nama_obat' => 'Prednisone 5mg', 'harga' => 5000, 'stok' => 40],
            ['nama_obat' => 'Ketoprofen 25mg', 'harga' => 7000, 'stok' => 15],
            ['nama_obat' => 'Diclofenac 50mg', 'harga' => 7500, 'stok' => 35],
            ['nama_obat' => 'Lansoprazole 30mg', 'harga' => 10500, 'stok' => 20],
            ['nama_obat' => 'Mefenamic Acid 500mg', 'harga' => 6500, 'stok' => 25],
            ['nama_obat' => 'Atorvastatin 20mg', 'harga' => 13000, 'stok' => 10],
            ['nama_obat' => 'Clopidogrel 75mg', 'harga' => 14000, 'stok' => 10],
        ];

        DB::table('data_obat')->insert($data);
    }
}
