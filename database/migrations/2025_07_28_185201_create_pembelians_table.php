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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('users');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('tanggal_pembelian');
            $table->decimal('total_harga', 12, 2);
            $table->string('status', 50); // misal: Dipesan, Diterima
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
