<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_perhitungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perhitungan_id')->constrained('perhitungans')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obats');
            $table->unsignedInteger('stok_saat_ini');
            $table->unsignedInteger('total_penjualan_terakhir'); // Jml penjualan yg jadi dasar perhitungan
            $table->unsignedInteger('rekomendasi_pembelian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_perhitungans');
    }
};
