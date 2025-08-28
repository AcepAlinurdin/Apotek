<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    // Tabel detail biasanya tidak memerlukan timestamps (created_at, updated_at)
    public $timestamps = false;

    protected $fillable = [
        'penjualan_id',
        'obat_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'satuan',
    ];

    /**
     * Setiap detail adalah bagian dari satu transaksi penjualan.
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    /**
     * Setiap detail merujuk pada satu jenis obat.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
