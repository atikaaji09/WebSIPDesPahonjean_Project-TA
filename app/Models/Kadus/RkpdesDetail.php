<?php

namespace App\Models\Kadus;

use Illuminate\Database\Eloquent\Model;

class RkpdesDetail extends Model
{
    protected $table = 'rkpdes_detail';

    protected $fillable = [
        'rkpdes_id',
        'rpjmdes_detail_id',
        'user_id',
        'dusun_id',

        'bidang_id',
        'sub_bidang_id',
        'kegiatan_id',

        'sdgs',
        'data_existing',
        'target_capaian',

        'lokasi',
        'volume',
        'satuan',

        'anggaran',
        'sumber_dana',

        'penerima_laki',
        'penerima_perempuan',
        'penerima_rtm',

        'waktu_pelaksanaan',
        'pelaksana_kegiatan',
        'rencana_tpk',
        'pola_pelaksanaan',

        'status',
        'status_progres',
    ];

    public function rkpdes()
    {
        return $this->belongsTo(Rkpdes::class);
    }

    public function rpjmdesDetail()
    {
        return $this->belongsTo(\App\Models\RpjmdesDetail::class, 'rpjmdes_detail_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(\App\Models\Kegiatan::class);
    }

    public function dusun()
    {
        return $this->belongsTo(\App\Models\Dusun::class);
    }

    public function bidang()
    {
        return $this->belongsTo(\App\Models\Bidang::class);
    }

    public function subBidang()
    {
        return $this->belongsTo(\App\Models\SubBidang::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getVolumeFormatAttribute()
    {
        if ($this->satuan == 'm2')
            return $this->volume . ' m<sup>2</sup>';
        if ($this->satuan == 'm3')
            return $this->volume . ' m<sup>3</sup>';
        return $this->volume . ' ' . $this->satuan;
    }
}
