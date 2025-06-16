<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataObat extends Model
{
    use HasFactory;

    protected $table = 'data_obat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Memastikan semua kolom dari form bisa diisi
    protected $fillable = [
        'tanggal',
        'kode_obat',
        'nama_obat',
        'kategori',
        'supplier',
        'qty', // Nama kolom di database untuk stok
        'harga_satuan',
        'harga_box'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // Memberitahu Laravel bahwa kolom 'tanggal' harus diperlakukan sebagai objek tanggal
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi one-to-many ke model PenjualanObat.
     */
    public function penjualan()
    {
        return $this->hasMany(PenjualanObat::class, 'obat_id');
    }
}
