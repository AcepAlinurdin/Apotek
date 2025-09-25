<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPaketObat extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'paket_obat_id',
        'obat_id',
        'jumlah',
    ];

    /**
     * Mendefinisikan relasi many-to-one ke PaketObat.
     * Setiap detail milik satu paket.
     */
    public function paketObat()
    {
        return $this->belongsTo(PaketObat::class);
    }

    /**
     * Mendefinisikan relasi many-to-one ke Obat.
     * Setiap detail menunjuk ke satu jenis obat.
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
