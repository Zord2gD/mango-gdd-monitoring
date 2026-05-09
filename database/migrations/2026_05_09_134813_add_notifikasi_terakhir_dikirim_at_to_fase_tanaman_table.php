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
        Schema::table('fase_tanaman', function (Blueprint $table) {
            $table->timestamp('notifikasi_terakhir_dikirim_at')->nullable()->after('fase_sekarang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fase_tanaman', function (Blueprint $table) {
            $table->dropColumn('notifikasi_terakhir_dikirim_at');
        });
    }
};
