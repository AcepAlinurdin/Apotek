<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_obat', function (Blueprint $table) {
            $table->id();
            $table->string('nama_obat', 100)->unique();
            $table->decimal('harga', 10, 2);
            $table->integer('stok')->default(0); // Stok awal 0
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_obat');
    }
};
