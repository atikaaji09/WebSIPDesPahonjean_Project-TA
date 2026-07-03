<?php

namespace App\Exports;

use App\Models\Admin\RkpdesDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    protected ?int $dusunId, $tahun;

    public function __construct(?int $dusunId, $tahun)
    {
        $this->dusunId = $dusunId;
        $this->tahun   = $tahun;
    }

    public function view(): View
    {
        $query = RkpdesDetail::with([
            'rkpdes',
            'bidang',
            'subBidang',
            'kegiatan',
            'user.dusun',
            'monitoring'
        ]);

        $tahun = $this->tahun;
        $dusunId = $this->dusunId;

        if ($tahun) {
            $query->whereHas('rkpdes', function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            });
        }

        if ($dusunId) {
            $query->whereHas('user', function ($q) use ($dusunId) {
                $q->where('dusun_id', $dusunId);
            });
        }

        return view('exports.laporan', [
            'data' => $query->get()
        ]);
    }
}
