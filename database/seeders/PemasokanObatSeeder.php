<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataObat;
use App\Models\PemasokanObat;

class PemasokanObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek atau buat obat jika belum ada
        $obat = DataObat::firstOrCreate(
            ['nama_obat' => 'Paracetamol 500mg'],
            ['harga' => 4000, 'stok' => 0]
        );

        // Tambahkan data pemasokan obat
        PemasokanObat::create([
            'obat_id' => $obat->id,
            'jumlah_pemasokan' => 50,
            'total_harga' => 200000,
            'tanggal_pemasokan' => '2024-03-10',
            'supplier' => 'PT Farma Sehat'
        ]);

        // Perbarui stok obat
        $obat->increment('stok', 50);
    }
}
