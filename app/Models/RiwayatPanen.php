<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPanen extends Model
{
    protected $fillable = [
        'kebun_id',
        'tanggal_berbunga',
        'tanggal_panen',
        'total_gdd',
        'hasil_panen_kg',
        'catatan'
    ];

    public function kebun()
    {
        return $this->belongsTo(Kebun::class);
    }
}
