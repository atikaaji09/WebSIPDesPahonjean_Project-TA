<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Rkpdes;
use App\Models\Admin\RkpdesDetail;
use App\Models\Dusun; // pastikan model Dusun ada
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanTahunanController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->input('entries', 10);

        $listTahun = Rkpdes::where('is_ditetapkan', 1)
            ->whereHas('details')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $dusunList = Dusun::all();

        $tahun = $request->tahun;
        $dusunId = $request->dusun_id;

        if (!$tahun) {

            $tahun = Rkpdes::where('is_ditetapkan', 1)
                ->whereHas('details')
                ->orderBy('tahun', 'desc')
                ->value('tahun');
        }

        if ($tahun && !$listTahun->contains($tahun)) {
            $tahun = null;
        }

        $data = RkpdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        if ($tahun) {

            $query = RkpdesDetail::with([
                'monitoring',
                'rkpdes',
                'bidang',
                'subBidang',
                'kegiatan',
                'user.dusun',
            ])
                ->whereHas('rkpdes', function ($q) use ($tahun) {
                    $q->where('tahun', $tahun)
                        ->where('is_ditetapkan', 1);
                });

            if ($dusunId) {

                $query->whereHas('user', function ($q) use ($dusunId) {
                    $q->where('dusun_id', $dusunId);
                });
            }

            $data = $query
                ->paginate($entries)
                ->withQueryString();
        }

        return view('admin.laporan', compact(
            'data',
            'listTahun',
            'tahun',
            'dusunList',
            'dusunId'
        ));
    }

    public function export(Request $request)
    {
        $dusunId = $request->dusun_id;
        $tahun   = $request->tahun;

        return Excel::download(
            new LaporanExport($dusunId, $tahun),
            'laporan-tahunan-rpjmdes.xlsx'
        );
    }
}
