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
        Schema::create('fase_tanaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kebun_id')->constrained('kebuns')->onDelete('cascade');
            $table->date('tanggal_berbunga');
            $table->enum('fase_sekarang', ['berbunga', 'buah_kecil', 'pembesaran', 'matang'])->default('berbunga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fase_tanaman');
    }
};
