<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketObat extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_paket',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke DetailPaketObat.
     * Satu paket bisa memiliki banyak detail obat.
     */
    public function detailPaketObats()
    {
        return $this->hasMany(DetailPaketObat::class);
    }
}
