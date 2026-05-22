<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index pada kolom-kolom yang sering digunakan dalam WHERE dan JOIN.
     * Ini meningkatkan kecepatan query secara signifikan tanpa mengubah struktur data.
     */
    public function up(): void
    {
        // Index pada kolom 'role' di tabel users
        // Digunakan: User::where('role', 'petani')->count()
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index('role', 'users_role_index');
            }
        });

        // Index pada kolom 'user_id' di tabel kebuns
        // Digunakan: Kebun::where('user_id', Auth::id())->get()
        Schema::table('kebuns', function (Blueprint $table) {
            if (!$this->indexExists('kebuns', 'kebuns_user_id_index')) {
                $table->index('user_id', 'kebuns_user_id_index');
            }
        });

        // Composite index pada kebun_id + tanggal di suhu_harian
        // Digunakan: SuhuHarian::where('kebun_id', x)->where('tanggal', '>=', y)->sum('gdd')
        Schema::table('suhu_harian', function (Blueprint $table) {
            if (!$this->indexExists('suhu_harian', 'suhu_harian_kebun_tanggal_index')) {
                $table->index(['kebun_id', 'tanggal'], 'suhu_harian_kebun_tanggal_index');
            }
        });

        // Index pada tanggal saja untuk query SuhuHarian::where('tanggal', today)
        Schema::table('suhu_harian', function (Blueprint $table) {
            if (!$this->indexExists('suhu_harian', 'suhu_harian_tanggal_index')) {
                $table->index('tanggal', 'suhu_harian_tanggal_index');
            }
        });

        // Index pada kebun_id di fase_tanaman
        // Digunakan: FaseTanaman::where('kebun_id', x)->first()
        Schema::table('fase_tanaman', function (Blueprint $table) {
            if (!$this->indexExists('fase_tanaman', 'fase_tanaman_kebun_id_index')) {
                $table->index('kebun_id', 'fase_tanaman_kebun_id_index');
            }
        });

        // Index pada kebun_id di riwayat_panens
        // Digunakan: RiwayatPanen::whereHas('kebun', ...) dan with('kebun')
        Schema::table('riwayat_panens', function (Blueprint $table) {
            if (!$this->indexExists('riwayat_panens', 'riwayat_panens_kebun_id_index')) {
                $table->index('kebun_id', 'riwayat_panens_kebun_id_index');
            }
        });
    }

    /**
     * Hapus semua index yang ditambahkan (rollback-safe).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });

        Schema::table('kebuns', function (Blueprint $table) {
            $table->dropIndex('kebuns_user_id_index');
        });

        Schema::table('suhu_harian', function (Blueprint $table) {
            $table->dropIndex('suhu_harian_kebun_tanggal_index');
            $table->dropIndex('suhu_harian_tanggal_index');
        });

        Schema::table('fase_tanaman', function (Blueprint $table) {
            $table->dropIndex('fase_tanaman_kebun_id_index');
        });

        Schema::table('riwayat_panens', function (Blueprint $table) {
            $table->dropIndex('riwayat_panens_kebun_id_index');
        });
    }

    /**
     * Cek apakah index sudah ada (idempotent — aman dijalankan ulang).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return !empty($indexes);
    }
};
