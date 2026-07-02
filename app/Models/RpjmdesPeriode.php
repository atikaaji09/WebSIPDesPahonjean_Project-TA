<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpjmdesPeriode extends Model
{
    protected $table = 'rpjmdes_periode';

    protected $fillable = [
        'tahun_mulai',
        'tahun_selesai',
        'nama_periode',
        'is_ditetapkan'
    ];

    // relasi ke detail
    public function details()
    {
        return $this->hasMany(RpjmdesDetail::class, 'rpjmdes_id');
    }
}
