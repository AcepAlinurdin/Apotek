<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataObat;
use Illuminate\Support\Facades\Route;

class DataObat extends Model
{
    use HasFactory;

    protected $table = 'data_obat';

    protected $fillable = ['nama_obat', 'harga', 'stok'];
}
