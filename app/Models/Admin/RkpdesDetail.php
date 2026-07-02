<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\RpjmdesDetail;
use App\Models\Bidang;
use App\Models\SubBidang;
use App\Models\Kegiatan;
use App\Models\User;
use App\Models\Admin\MonitoringRkpdes;
use App\Models\Admin\Rkpdes;

class RkpdesDetail extends Model
{
    protected $table = 'rkpdes_detail';

    protected $fillable = [
        'rkpdes_id',
        'rpjmdes_detail_id',
        'user_id',
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
        'status_progres'
    ];

    public function rkpdes()
    {
        return $this->belongsTo(Rkpdes::class);
    }

    public function rpjmdesDetail()
    {
        return $this->belongsTo(RpjmdesDetail::class, 'rpjmdes_detail_id');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function monitoring()
    {
        return $this->hasMany(MonitoringRkpdes::class);
    }

    public function getSatuanFormatAttribute()
    {
        return match ($this->satuan) {
            'm2' => 'm<sup>2</sup>',
            'm3' => 'm<sup>3</sup>',
            default => $this->satuan
        };
    }
}
