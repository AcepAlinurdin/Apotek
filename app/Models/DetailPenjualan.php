<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    /**
     * Menonaktifkan timestamps (created_at, updated_at) jika tabel Anda tidak memilikinya.
     * Jika ada, hapus atau komentari baris ini.
     */
    public $timestamps = false; 

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'penjualan_id',
        'obat_id', // <-- INI ADALAH KUNCI MASALAHNYA. PASTIKAN ADA DI SINI.
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * Mendefinisikan relasi ke model Obat.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    /**
     * Mendefinisikan relasi ke model Penjualan.
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
