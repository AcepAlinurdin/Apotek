<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemasokan_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('data_obat')->onDelete('cascade');
            $table->integer('jumlah_pemasokan');
            $table->decimal('total_harga', 10, 2);
            $table->date('tanggal_pemasokan');
            $table->string('supplier', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasokan_obat');
    }
};
