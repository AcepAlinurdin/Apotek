<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanObat extends Model
{
    use HasFactory;

    protected $table = 'penjualan_obat';
    protected $fillable = ['obat_id', 'jumlah_pembelian', 'total_harga', 'tanggal_penjualan'];

    public function obat()
    {
        return $this->belongsTo(DataObat::class, 'obat_id');
    }

    // Fungsi untuk mengurangi stok saat terjadi penjualan
    protected static function boot()
    {
        parent::boot();
        static::created(function ($penjualan) {
            $penjualan->obat->decrement('stok', $penjualan->jumlah_pembelian);
        });
    }
}
