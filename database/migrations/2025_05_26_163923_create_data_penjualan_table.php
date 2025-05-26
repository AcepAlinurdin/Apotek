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
        Schema::create('data_penjualan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');              // tanggal penjualan
            $table->string('nama_obat');          // nama obat
            $table->integer('qty');                // jumlah obat terjual
            $table->string('kode_obat', 50);      // kode obat, maksimal 50 karakter
            $table->timestamps();                  // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_penjualan');
    }
};
