<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kebun extends Model
{
    protected $fillable = [
        'user_id',
        'nama_kebun',
        'lokasi',
        'latitude',
        'longitude',
        'jumlah_pohon',
        'jenis_mangga'
    ];

    protected $appends = ['target_gdd', 'total_gdd', 'gdd_progress', 'is_siap_panen', 'fase_otomatis'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fase()
    {
        return $this->hasOne(FaseTanaman::class);
    }
    public function suhu()
    {
        return $this->hasMany(SuhuHarian::class);
    }

    public function riwayatPanen()
    {
        return $this->hasMany(RiwayatPanen::class);
    }

    public function getTargetGddAttribute()
    {
        $jenis = strtolower($this->jenis_mangga);

        // Varietas Indramayu
        if (str_contains($jenis, 'cengkir')) return 1150;
        if (str_contains($jenis, 'dermayu')) return 1100;
        if (str_contains($jenis, 'gedong gincu')) return 1100;
        if (str_contains($jenis, 'lalijiwa')) return 1200;

        // Varietas Populer
        if (str_contains($jenis, 'gedong')) return 1100;
        if (str_contains($jenis, 'harumanis') || str_contains($jenis, 'arum manis')) return 1200;
        if (str_contains($jenis, 'golek')) return 1250;
        if (str_contains($jenis, 'manalagi')) return 1150;
        if (str_contains($jenis, 'gajah')) return 1300;
        if (str_contains($jenis, 'kweni')) return 1100;
        if (str_contains($jenis, 'kopyor')) return 1200;

        return 1000; // Default
    }

    public function suhuAktif()
    {
        return $this->hasMany(SuhuHarian::class, 'kebun_id', 'id')
            ->join('fase_tanaman', 'fase_tanaman.kebun_id', '=', 'suhu_harian.kebun_id')
            ->whereColumn('suhu_harian.tanggal', '>=', 'fase_tanaman.tanggal_berbunga');
    }

    public function getTotalGddAttribute()
    {
        // 1. If pre-calculated via database aggregation (withSum), use it directly (O(1) Memory & Time)
        if (array_key_exists('total_gdd_db', $this->attributes)) {
            return (float) ($this->attributes['total_gdd_db'] ?? 0);
        }

        $fase = $this->fase;
        if (!$fase || !$fase->tanggal_berbunga) {
            return 0;
        }

        // 2. Hybrid Cache System: remember for 24 hours (86400 seconds)
        return (float) \Illuminate\Support\Facades\Cache::remember("gdd_total_{$this->id}", 86400, function () use ($fase) {
            // If 'suhu' is eagerly loaded, calculate from the collection to prevent N+1 query issues
            if ($this->relationLoaded('suhu')) {
                return $this->suhu
                    ->where('tanggal', '>=', $fase->tanggal_berbunga)
                    ->sum('gdd');
            }

            // Fallback to database query if not eager loaded
            return $this->suhu()
                ->where('tanggal', '>=', $fase->tanggal_berbunga)
                ->sum('gdd');
        });
    }

    public function getGddProgressAttribute()
    {
        $total = $this->total_gdd;
        $target = $this->target_gdd;
        
        $progress = ($total / $target) * 100;
        return min(100, round($progress, 2));
    }

    public function getIsSiapPanenAttribute()
    {
        return $this->total_gdd >= $this->target_gdd;
    }

    public function getFaseOtomatisAttribute()
    {
        if (!$this->fase || !$this->fase->tanggal_berbunga) {
            return 'Belum Berbunga';
        }

        $progress = $this->gdd_progress;
        if ($progress < 25) return 'Berbunga';
        if ($progress < 50) return 'Pembentukan Buah (Pentil)';
        if ($progress < 75) return 'Pembesaran Buah';
        if ($progress < 100) return 'Pematangan';
        return 'Siap Panen';
    }
}
