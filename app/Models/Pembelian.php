<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pembelians';

    /**
     * Atribut yang dapat diisi secara massal.
     * INI ADALAH BAGIAN PALING PENTING UNTUK DIPERBAIKI.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pegawai_id',
        'supplier_id',
        'tanggal_pembelian',
        'total_harga',
        'status',
    ];

    /**
     * Relasi ke pegawai (user).
     */
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    /**
     * Relasi ke supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke detail pembelian.
     */
    public function details()
    {
        return $this->hasMany(DetailPembelian::class);
    }
}