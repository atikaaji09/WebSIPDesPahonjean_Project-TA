<?php

namespace App\Http\Controllers\Kadus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\RkpdesDetail;
use App\Models\Admin\Rkpdes;
use App\Models\Dusun;

class KadusRkpdesController extends Controller
{
    public function index(Request $request)
    {
        $selectedTahun = $request->tahun;
        $entries = $request->get('entries', 10);
        $dusunId = $request->dusun_id;

        $dusunList = Dusun::all();

        $tahunList = Rkpdes::where('is_ditetapkan', 1)
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // JIKA BELUM PILIH TAHUN
        // OTOMATIS AMBIL TAHUN TERBARU
        if (!$selectedTahun && $tahunList->count()) {

            $selectedTahun = $tahunList->first();
        }

        // JIKA TAHUN TIDAK VALID
        if ($selectedTahun && !$tahunList->contains($selectedTahun)) {

            $selectedTahun = $tahunList->first();
        }

        $rkpdesDetails = RkpdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        if ($selectedTahun) {

            $query = RkpdesDetail::with([
                'rpjmdesDetail.usulan.rtrw.dusun',
                'rpjmdesDetail.bidang',
                'rpjmdesDetail.subBidang',
                'rpjmdesDetail.kegiatan'
            ])
                ->whereHas('rkpdes', function ($q) use ($selectedTahun) {

                    $q->where('tahun', $selectedTahun)
                        ->where('is_ditetapkan', 1);
                });

            if ($dusunId) {

                $query->whereHas('rpjmdesDetail.usulan.rtrw', function ($q) use ($dusunId) {

                    $q->where('dusun_id', $dusunId);
                });
            }

            $rkpdesDetails = $query
                ->paginate($entries)
                ->withQueryString();
        }

        return view('kadus.rkp', compact(
            'rkpdesDetails',
            'tahunList',
            'selectedTahun',
            'dusunList',
            'dusunId'
        ));
    }
}
