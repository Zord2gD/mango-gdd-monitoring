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
        Schema::create('riwayat_panens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kebun_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_berbunga')->nullable();
            $table->date('tanggal_panen');
            $table->decimal('total_gdd', 8, 2);
            $table->decimal('hasil_panen_kg', 10, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_panens');
    }
};
