<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal', // Tambahkan kolom tanggal
        'supplier_id',
        'nama_obat',
        'kategori',
        'stok',
        'harga_satuan',
        'harga_box',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Supplier.
     * Setiap obat dimiliki oleh satu supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Mendefinisikan relasi "hasMany" ke model DetailPenjualan.
     * Satu jenis obat bisa muncul di banyak detail penjualan.
     */
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}
