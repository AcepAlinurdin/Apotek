<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('data_obat')->onDelete('cascade');
            $table->integer('jumlah_pembelian');
            $table->decimal('total_harga', 10, 2);
            $table->date('tanggal_penjualan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_obat');
    }
};
