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
    Schema::table('detail_pembelians', function (Blueprint $table) {
        $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pembelians', function (Blueprint $table) {
            //
        });
    }
};
