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
        Schema::create('data_obat', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');               // tanggal data obat masuk
            $table->string('nama_obat');           // nama obat
            $table->integer('qty');                // jumlah obat
            $table->string('kode_obat', 50);       // kode obat
            $table->string('supplier');            // nama supplier
            $table->decimal('harga_satuan', 12, 2); // harga satuan
            $table->decimal('harga_box', 12, 2)->nullable(); // harga box, bisa null
            $table->timestamps();                  // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_obat');
    }
};
