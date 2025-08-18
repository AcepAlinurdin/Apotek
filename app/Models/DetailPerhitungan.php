<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPerhitungan extends Model
{
    use HasFactory;

    protected $table = 'detail_perhitungans';

    /**
     * Atribut yang dapat diisi secara massal.
     * Ini adalah "daftar tamu" yang mengizinkan kolom-kolom ini untuk disimpan.
     */
    protected $fillable = [
        'perhitungan_id',
        'obat_id',
        'stok_saat_ini',
        'total_penjualan_terakhir',
        'rekomendasi_pembelian',
    ];

    /**
     * Relasi ke perhitungan.
     */
    public function perhitungan(): BelongsTo
    {
        return $this->belongsTo(Perhitungan::class);
    }
    
    /**
     * Relasi ke obat.
     */
    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}