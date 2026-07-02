<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'sub_bidang_id',
        'nama_kegiatan'
    ];

    public function subBidang()
    {
        return $this->belongsTo(SubBidang::class);
    }
}
