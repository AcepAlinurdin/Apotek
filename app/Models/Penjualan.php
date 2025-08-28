<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tanggal_penjualan',
        'total_harga',
        'satuan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_penjualan' => 'datetime',
    ];

    /**
     * [FIXED] Mendefinisikan relasi ke model User.
     * Setiap penjualan dilayani oleh satu user.
     * Foreign key-nya adalah 'pegawai_id'.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    /**
     * Satu penjualan memiliki banyak item detail.
     */
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}
