<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuhuHarian extends Model
{
    protected $table = 'suhu_harian';

    protected $fillable = [
        'kebun_id',
        'tanggal',
        'tmin',
        'tmax',
        'gdd'
    ];

    public function kebun()
    {
        return $this->belongsTo(Kebun::class);
    }
}
