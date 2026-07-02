<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rtrw extends Model
{
    protected $table = 'rt_rw';

    protected $fillable = [
        'dusun_id',
        'rt',
        'rw'
    ];

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }
}
