<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'alamat',
        'telepon',
    ];

    /**
     * Satu supplier bisa memasok banyak obat.
     */
    public function obats()
    {
        return $this->hasMany(Obat::class);
    }
}
