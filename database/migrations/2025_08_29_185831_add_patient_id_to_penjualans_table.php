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
        Schema::table('penjualans', function (Blueprint $table) {
            // Menambahkan kolom id_pasien SETELAH kolom pegawai_id yang benar
            $table->foreignId('id_pasien')
                  ->nullable()
                  ->after('pegawai_id') // <-- DIUBAH ke kolom yang benar
                  ->constrained('pasiens')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['id_pasien']);
            $table->dropColumn('id_pasien');
        });
    }
};