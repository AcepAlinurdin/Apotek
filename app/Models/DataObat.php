<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataObat extends Model
{
    use HasFactory;

    protected $table = 'data_obat'; // Pastikan sama dengan nama tabel di database
    protected $fillable = ['nama_obat', 'harga_satuan', 'stok'];

    /**
     * Relasi one-to-many ke model Penjualan
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'obat_id');
    }
}