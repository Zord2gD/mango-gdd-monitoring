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
        Schema::create('suhu_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kebun_id')->constrained('kebuns')->onDelete('cascade');
            $table->date('tanggal');
            $table->float('tmin');
            $table->float('tmax');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suhu_harian');
    }
};
