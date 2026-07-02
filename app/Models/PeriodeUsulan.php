<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeUsulan extends Model
{
    protected $table = 'periode_usulan';

    protected $fillable = [
        'nama_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];
}
