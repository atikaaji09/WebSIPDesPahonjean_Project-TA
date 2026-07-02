<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\RkpdesDetail;

class RpjmdesDetail extends Model
{
    protected $table = 'rpjmdes_detail';

    protected $fillable = [
        'usulan_id',
        'rpjmdes_id',
        'bidang_id',
        'sub_bidang_id',
        'kegiatan_id',
        'sasaran_manfaat',
        'tahun_pelaksanaan',
        'anggaran',
        'sumber',
        'pola_pelaksanaan',
        'status',
        'volume'
    ];

    protected $casts = [
        'tahun_pelaksanaan' => 'array',
    ];

    // ================= RELASI =================

    public function usulan()
    {
        return $this->belongsTo(Usulan::class, 'usulan_id');
    }

    public function periode()
    {
        return $this->belongsTo(RpjmdesPeriode::class, 'rpjmdes_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function subBidang()
    {
        return $this->belongsTo(SubBidang::class, 'sub_bidang_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function rkpdesDetails()
    {
        return $this->hasMany(RkpdesDetail::class, 'rpjmdes_detail_id');
    }
}
