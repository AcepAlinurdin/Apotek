<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'stoks';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'obat_id',

        'jumlah',
        'tanggal',
    ];

    /**
     * Relasi dengan model Obat.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
