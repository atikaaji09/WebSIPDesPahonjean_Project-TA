<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringRkpdes extends Model
{
    use HasFactory;

    protected $table = 'monitoring_rkpdes';

    protected $fillable = [
        'rkpdes_detail_id',
        'volume_realisasi',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'volume_realisasi' => 'decimal:2',
    ];

    public function rkpdesDetail()
    {
        return $this->belongsTo(RkpdesDetail::class);
    }
}
