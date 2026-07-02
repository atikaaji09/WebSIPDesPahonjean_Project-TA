<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Rkpdes;
use App\Models\Admin\RkpdesDetail;
use App\Models\Dusun;
use App\Exports\RkpdesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\MonitoringRkpdes;
use App\Exports\SusunRkpdesExport;

class MonitoringRkpController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->input('entries', 10);

        $tahunList = Rkpdes::where('is_ditetapkan', true)
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $dusunList = Dusun::all();

        // TAHUN YANG DIPILIH
        $selectedTahun = $request->tahun;

        // JIKA BELUM PILIH TAHUN
        // AMBIL TAHUN TERBARU
        if (!$selectedTahun) {

            $latestRkpdes = Rkpdes::where('is_ditetapkan', true)
                ->orderBy('tahun', 'desc')
                ->first();

            $selectedTahun = $latestRkpdes
                ? $latestRkpdes->tahun
                : null;
        }

        $dusunId = $request->dusun_id ?? null;

        $rkpdesDetails = RkpdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        // JIKA ADA TAHUN
        if ($selectedTahun) {

            $rkpdes = Rkpdes::where('tahun', $selectedTahun)
                ->where('is_ditetapkan', true)
                ->first();

            if ($rkpdes) {

                $query = RkpdesDetail::with([
                    'monitoring',
                    'rkpdes',
                    'rpjmdesDetail.bidang',
                    'rpjmdesDetail.subBidang',
                    'rpjmdesDetail.kegiatan',
                    'rpjmdesDetail.usulan.rtrw.dusun',
                    'user.dusun'
                ])
                    ->where('rkpdes_id', $rkpdes->id);

                // FILTER DUSUN
                if ($dusunId) {

                    $query->whereHas('user', function ($q) use ($dusunId) {
                        $q->where('dusun_id', $dusunId);
                    });
                }

                $rkpdesDetails = $query
                    ->paginate($entries)
                    ->withQueryString();

                $rkpdesDetails->getCollection()->transform(function ($item) {

                    $tahunSekarang = $item->rkpdes->tahun;

                    $anggaranTahunIni = $item->anggaran ?? 0;

                    $totalVolumeSemuaTahun =
                        \App\Models\Admin\RkpdesDetail::where(
                            'rpjmdes_detail_id',
                            $item->rpjmdes_detail_id
                        )->sum('volume');

                    $volumeRaw = strtolower(
                        trim($item->rpjmdesDetail->usulan->volume ?? '0')
                    );

                    // HITUNG TARGET VOLUME
                    if (str_contains($volumeRaw, 'x')) {

                        $dim = explode('x', $volumeRaw);

                        $volumeTarget = array_product($dim);
                    } else {

                        $volumeTarget = (float) preg_replace(
                            '/[^0-9.]/',
                            '',
                            $volumeRaw
                        );
                    }

                    // HITUNG ANGGARAN TAHUN DEPAN
                    $anggaranTahunDepan =
                        \App\Models\Admin\RkpdesDetail::where(
                            'rpjmdes_detail_id',
                            $item->rpjmdes_detail_id
                        )
                        ->whereHas('rkpdes', function ($q) use ($tahunSekarang) {
                            $q->where('tahun', '>', $tahunSekarang);
                        })
                        ->sum('anggaran');

                    // ANGGARAN EFEKTIF
                    if ($totalVolumeSemuaTahun == $volumeTarget) {

                        $item->anggaran_efektif = $anggaranTahunIni;
                    } else {

                        $hasil = $anggaranTahunIni - $anggaranTahunDepan;

                        if ($hasil <= 0) {

                            $item->anggaran_efektif = $anggaranTahunIni;
                        } else {

                            $item->anggaran_efektif = $hasil;
                        }
                    }

                    return $item;
                });
            }
        }

        return view('admin.monitoringrkp', compact(
            'tahunList',
            'selectedTahun',
            'dusunList',
            'dusunId',
            'rkpdesDetails'
        ));
    }

    public function storeProgres(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rkpdes_detail,id',
            'volume_realisasi' => 'required|numeric|min:0',
            'satuan' => 'required'
        ]);

        $item = RkpdesDetail::findOrFail($request->id);

        $target = (float) $item->volume;

        $totalRealisasiSebelumnya = MonitoringRkpdes::where('rkpdes_detail_id', $item->id)
            ->sum('volume_realisasi');

        $totalSetelahInput = $totalRealisasiSebelumnya + $request->volume_realisasi;

        if ($totalSetelahInput > $target) {
            return back()->with('error', 'Volume realisasi tidak boleh melebihi volume target.');
        }

        MonitoringRkpdes::create([
            'rkpdes_detail_id' => $item->id,
            'volume_realisasi' => $request->volume_realisasi,
            'tanggal' => now()
        ]);

        $totalRealisasi = $totalSetelahInput;

        $tahunSekarang = $item->rkpdes->tahun ?? null;
        $lanjutan = false;

        if ($tahunSekarang) {
            $lanjutan = RkpdesDetail::where('rpjmdes_detail_id', $item->rpjmdes_detail_id)
                ->whereHas('rkpdes', function ($q) use ($tahunSekarang) {
                    $q->where('tahun', '>', $tahunSekarang);
                })
                ->exists();
        }

        if ($totalRealisasi <= 0) {
            $status = 'baru';
        } elseif ($totalRealisasi < $target && $lanjutan) {
            $status = 'lanjutan';
        } elseif ($totalRealisasi < $target) {
            $status = 'diproses';
        } else {
            $status = 'selesai';
        }

        $item->update([
            'status_progres' => $status,
            'satuan' => $request->satuan
        ]);

        return back()->with('success', 'Progres berhasil disimpan');
    }

    public function export(Request $request)
    {
        return Excel::download(
            new RkpdesExport($request->tahun, $request->dusun_id),
            'laporan-rkpdes.xlsx'
        );
    }

    public function exportsusun(Request $request)
    {
        return Excel::download(
            new RkpdesExport($request->tahun, $request->dusun_id),
            'penyusunan-rkpdes.xlsx'
        );
    }
}
