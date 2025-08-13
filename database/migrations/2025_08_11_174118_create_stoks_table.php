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
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obat_id');
            $table->string('tipe_pergerakan'); // misal: 'masuk', 'keluar'
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
             $table->date('tanggal');
            $table->timestamps();

            $table->foreign('obat_id')->references('id')->on('obats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
