<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemasokanObat extends Model
{
    use HasFactory;
    protected $table = 'pemasokan_obat';
    protected $fillable = ['obat_id', 'jumlah_pemasokan', 'total_harga', 'tanggal_pemasokan', 'supplier'];

    public function obat()
    {
        return $this->belongsTo(DataObat::class, 'obat_id');
    }
}
