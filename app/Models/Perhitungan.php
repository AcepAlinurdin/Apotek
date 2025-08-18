<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perhitungan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'perhitungans';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pegawai_id',
        'tanggal_perhitungan',
        'hasil_json'
    ];

    /**
     * Relasi ke detail perhitungan (satu perhitungan memiliki banyak detail).
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailPerhitungan::class);
    }
}