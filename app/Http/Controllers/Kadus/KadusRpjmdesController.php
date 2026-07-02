<?php

namespace App\Http\Controllers\Kadus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpjmdesPeriode;
use App\Models\RpjmdesDetail;
use App\Models\Dusun;

class KadusRpjmdesController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->get('entries', 10);
        $dusunId = $request->dusun_id;

        $dusunList = Dusun::all();
        $periodes = RpjmdesPeriode::where('is_ditetapkan', 1)
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        $periodeId = $request->periode_id;

        if (!$periodeId && $periodes->count()) {
            $periodeId = $periodes->first()->id;
        }

        if ($periodeId && !$periodes->pluck('id')->contains($periodeId)) {
            $periodeId = $periodes->first()->id ?? null;
        }

        $data = RpjmdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        if ($periodeId) {

            $query = RpjmdesDetail::with([
                'usulan.rtrw.dusun',
                'bidang',
                'subBidang',
                'kegiatan',
                'rkpdesDetails.monitoring'
            ])
                ->where('rpjmdes_id', $periodeId);

            if ($dusunId) {
                $query->whereHas('usulan.rtrw', function ($q) use ($dusunId) {
                    $q->where('dusun_id', $dusunId);
                });
            }

            $data = $query
                ->paginate($entries)
                ->withQueryString();

            $data->getCollection()->transform(function ($item) {

                $volume = $item->usulan->volume ?? '0';
                $satuan = $item->usulan->satuan ?? '';

                $terpakai = $item->rkpdesDetails->sum(function ($detail) {
                    return $detail->monitoring->sum('volume_realisasi');
                });

                $isDimensi = in_array($satuan, ['m2', 'm3']) && str_contains($volume, 'x');

                if ($isDimensi) {

                    $dimensi = explode('x', $volume);

                    if (count($dimensi) == 3) {

                        $hasil = $dimensi[0] * $dimensi[1] * $dimensi[2];
                    } elseif (count($dimensi) == 2) {

                        $hasil = $dimensi[0] * $dimensi[1];
                    } else {

                        $hasil = (float) $volume;
                    }
                } else {

                    $hasil = (float) $volume;
                }

                $sisa = max(0, $hasil - $terpakai);

                if ($terpakai > 0) {

                    if ($sisa == 0) {

                        $item->status = 'masuk_rkpdes';
                    } else {

                        $item->status = 'lanjutan';
                    }
                }

                $item->volume_hasil = $hasil;
                $item->volume_asli = $volume;
                $item->is_dimensi = $isDimensi;
                $item->sisa = $sisa;

                // TAMBAHAN
                $item->volume_terpakai = $terpakai;

                return $item;
            });
        }

        return view('kadus.rpjm', compact(
            'periodes',
            'data',
            'dusunList',
            'dusunId',
            'periodeId'
        ));
    }
}
