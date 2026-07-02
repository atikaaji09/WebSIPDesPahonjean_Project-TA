<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usulan extends Model
{
    protected $table = 'usulan';

    protected $fillable = [
        'no_usulan',
        'nama_lengkap',
        'rt_rw_id',
        'dusun_id',
        'gagasan_kegiatan',
        'lokasi',
        'volume',
        'satuan',
        'penerima_laki',
        'penerima_perempuan',
        'penerima_rtm',
        'status'
    ];

    public function rtrw()
    {
        return $this->belongsTo(Rtrw::class, 'rt_rw_id');
    }

    public function rpjmdesDetail()
    {
        return $this->hasOne(RpjmdesDetail::class, 'usulan_id');
    }

    public function getVolumeHitungAttribute()
    {
        $dimensi = explode('x', $this->volume);

        // kalau bukan satuan meter → tidak dihitung
        if (!in_array($this->satuan, ['m', 'm2', 'm3'])) {
            return $this->volume;
        }

        if (count($dimensi) == 3) {
            return $dimensi[0] * $dimensi[1] * $dimensi[2];
        } elseif (count($dimensi) == 2) {
            return $dimensi[0] * $dimensi[1];
        } elseif (count($dimensi) == 1) {
            return $dimensi[0];
        }

        return '-';
    }

    public function getSatuanFormatAttribute()
    {
        if ($this->satuan == 'm2') return 'm<sup>2</sup>';
        if ($this->satuan == 'm3') return 'm<sup>3</sup>';
        return $this->satuan;
    }
}
