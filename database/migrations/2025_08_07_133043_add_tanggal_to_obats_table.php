<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_tanggal_to_obats_table.php
public function up(): void
{
    Schema::table('obats', function (Blueprint $table) {
        // Tambahkan kolom tanggal setelah kolom supplier_id
        $table->date('tanggal')->after('supplier_id')->nullable();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            //
        });
    }
};
