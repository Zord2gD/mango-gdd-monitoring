<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaseTanaman extends Model
{
    protected $table = 'fase_tanaman';
    protected $fillable = [
        'kebun_id',
        'tanggal_berbunga',
        'fase_sekarang'
    ];
    public function kebun()
    {
        return $this->belongsTo(Kebun::class);
    }
    
}
