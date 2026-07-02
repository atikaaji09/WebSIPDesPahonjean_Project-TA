<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Rkpdes extends Model
{
    protected $table = 'rkpdes';

    protected $fillable = [
        'tahun',
        'is_ditetapkan'
    ];

    public function details()
    {
        return $this->hasMany(RkpdesDetail::class, 'rkpdes_id');
    }
}
