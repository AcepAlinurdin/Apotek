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
        Schema::create('data_pembelian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');               // tanggal pembelian
            $table->string('nama_obat');           // nama obat
            $table->integer('qty');                 // jumlah obat yang dibeli (biasanya dalam satuan)
            $table->string('kode_obat', 50);       // kode obat
            $table->string('supplier');             // nama supplier
            $table->decimal('harga_satuan', 12, 2); // harga per satuan obat
            $table->decimal('harga_box', 12, 2)->nullable(); // harga per box obat, nullable kalau tidak ada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pembelian');
    }
};
