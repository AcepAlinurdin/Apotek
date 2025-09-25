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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap pasien (id_pasien)
            $table->string('nama_lengkap');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L: Laki-laki, P: Perempuan');
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('nomor_ktp', 16)->unique()->nullable(); // unique untuk mencegah duplikat, nullable karena opsional
            $table->text('catatan_alergi')->nullable(); // Sangat penting untuk mencatat alergi obat
            $table->timestamps(); // Membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};