<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanObat extends Model
{
    use HasFactory;

    protected $table = 'data_penjualan'; // Pastikan ini sesuai dengan nama tabel Anda

    protected $fillable = [
        'obat_id',
        'kode_obat',
        'nama_obat',
        'qty',
        // 'harga_satuan', // HAPUS BARIS INI
        'total_harga',   // PASTIKAN BARIS INI ADA
        'tanggal',
    ];

    /**
     * Mendefinisikan relasi ke model DataObat.
     */
    public function obat()
    {
        return $this->belongsTo(DataObat::class, 'obat_id');
    }
}