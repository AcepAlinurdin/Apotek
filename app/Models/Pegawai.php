<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pegawai',
        'jabatan',
    ];

    /**
     * Satu pegawai bisa melayani banyak transaksi penjualan.
     */
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

    /**
     * Satu pegawai bisa melakukan banyak transaksi pembelian.
     */
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
