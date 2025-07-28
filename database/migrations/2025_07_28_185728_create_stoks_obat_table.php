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
        Schema::create('riwayat_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obats');
            
            // Kolom ini akan diisi jika stok berubah karena pembelian/penjualan
            $table->foreignId('detail_pembelian_id')->nullable()->constrained('detail_pembelians');
            $table->foreignId('detail_penjualan_id')->nullable()->constrained('detail_penjualans');
            
            $table->string('tipe'); // 'masuk' atau 'keluar'
            $table->integer('jumlah'); // Jumlah yang masuk/keluar. Bisa positif atau negatif
            $table->text('keterangan'); // Cth: "Pembelian dari Supplier X" atau "Penjualan"
            
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            
            $table->timestamp('tanggal')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_stoks');
    }
};
