<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetDesa extends Model
{
    use HasFactory;

    protected $table = 'aset';

    protected $fillable = [
        'klas_aset',
        'nama_aset',
        'jenis_kepemilikan',
        'nomor_kepemilikan',
        'tanggal_kepemilikan',
        'kode_aset',
        'tahun_perolehan',
        'nilai_perolehan',
        'kondisi_aset',
        'keterangan',
        'luas',
        'bukti_kepemilikan',
        'titik_pangkal',
        'titik_ujung'
    ];
}
