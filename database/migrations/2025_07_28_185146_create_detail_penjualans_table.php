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
        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obats');
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_satuan', 10, 2); // Harga saat transaksi
            $table->decimal('subtotal', 12, 2);
            // timestamps tidak wajib untuk tabel detail, tapi bisa ditambahkan jika perlu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualans');
    }
};
