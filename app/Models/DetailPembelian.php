<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;
 public $timestamps = false;
    protected $fillable = [
        'pembelian_id',
        'obat_id',
        'jumlah',
        'harga_beli_satuan',
        'subtotal',
    ];
}