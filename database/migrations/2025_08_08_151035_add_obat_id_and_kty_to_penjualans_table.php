<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // 1. Tambah kolom foreign key untuk obat_id
            // Pastikan tipe data sama dengan primary key di tabel 'obats' (biasanya unsignedBigInteger)
            // ->after('id') bersifat opsional, hanya untuk menempatkan kolom setelah kolom 'id' penjualan
            $table->unsignedBigInteger('obat_id')->after('id')->nullable();

            // 2. Tambah kolom untuk kuantitas (kty)
            $table->integer('kty')->after('obat_id');

            // 3. Definisikan constraint foreign key
            // Ini akan menghubungkan 'obat_id' di tabel ini ke kolom 'id' di tabel 'obats'
            // onDelete('cascade') berarti jika data obat dihapus, data penjualan terkait juga akan terhapus.
            // Anda bisa menggantinya dengan onDelete('set null') jika ingin kolom obat_id menjadi NULL.
            $table->foreign('obat_id')->references('id')->on('obats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // 1. Hapus constraint foreign key terlebih dahulu
            // Laravel secara default menamainya 'penjualans_obat_id_foreign'
            $table->dropForeign(['obat_id']);

            // 2. Hapus kedua kolom
            $table->dropColumn(['obat_id', 'kty']);
        });
    }
};