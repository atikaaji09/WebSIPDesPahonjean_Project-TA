<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Rkpdes;
use App\Models\Admin\RkpdesDetail;
use App\Models\RpjmdesPeriode;
use App\Models\RpjmdesDetail;
use App\Models\Dusun;
use App\Models\Admin\MonitoringRkpdes;
use App\Exports\SusunRkpdesExport;
use Maatwebsite\Excel\Facades\Excel;

class SusunRkpController extends Controller
{

    public function index(Request $request)
    {
        $tahunSekarang = date('Y');
        $tahun = $request->tahun ? (int) $request->tahun : null;
        $dusunId = $request->dusun_id;

        $dusunList = Dusun::all();

        $periode = RpjmdesPeriode::where('is_ditetapkan', true)
            ->where('tahun_mulai', '<=', $tahunSekarang)
            ->where('tahun_selesai', '>=', $tahunSekarang)
            ->first();

        if (!$periode) {

            $tahunList = collect();
        } else {

            $tahunRpjm = RpjmdesDetail::where('rpjmdes_id', $periode->id)
                ->pluck('tahun_pelaksanaan')
                ->flatMap(function ($item) {
                    if (is_array($item)) {
                        return $item;
                    }

                    $decoded = json_decode($item, true);

                    if (is_array($decoded)) {
                        return $decoded;
                    }

                    return [$item];
                })
                ->filter()
                ->map(fn($tahun) => (int) $tahun)
                ->filter(fn($tahun) => $tahun > 0)
                ->unique()
                ->values();

            $tahunSudahDitetapkan = Rkpdes::where('is_ditetapkan', 1)
                ->pluck('tahun')
                ->map(fn($tahun) => (int) $tahun)
                ->filter(fn($tahun) => $tahun > 0)
                ->unique()
                ->values();

            $tahunList = $tahunRpjm
                ->diff($tahunSudahDitetapkan)
                ->sort()
                ->values();

            if (!$tahun && $tahunList->isNotEmpty()) {
                $tahun = $tahunList->last();
            }

            if ($tahun && !$tahunList->contains($tahun)) {
                $tahun = null;
            }
        }

        $query = RkpdesDetail::with([
            'bidang',
            'subBidang',
            'kegiatan',
            'rpjmdesDetail.usulan.rtrw.dusun'
        ])
            ->where('status', 'diajukan');

        $rkpdes = null;

        if ($tahun) {
            $rkpdes = Rkpdes::where('tahun', $tahun)
                ->where('is_ditetapkan', 0)
                ->first();
        }

        if ($tahun && $rkpdes) {
            $query->where('rkpdes_id', $rkpdes->id);
        } else {
            $query->whereRaw('1=0');
        }

        if ($dusunId) {
            $query->whereHas('rpjmdesDetail.usulan.rtrw.dusun', function ($q) use ($dusunId) {
                $q->where('id', $dusunId);
            });
        }

        $entries = $request->entries ?? 10;

        $details = $tahun
            ? $query->paginate($entries)->withQueryString()
            : collect();

        if ($details instanceof \Illuminate\Pagination\LengthAwarePaginator) {

            $details->getCollection()->transform(function ($item) {

                $rpjmId = $item->rpjmdes_detail_id;

                $totalRpjm = $item->rpjmdesDetail->anggaran ?? 0;

                $totalTerpakai = \App\Models\Admin\RkpdesDetail::where('rpjmdes_detail_id', $rpjmId)
                    ->sum('anggaran');

                $sisa = $totalRpjm - $totalTerpakai;

                $item->total_anggaran_rpjm = $totalRpjm;
                $item->total_anggaran_terpakai = $totalTerpakai;
                $item->sisa_anggaran = $sisa;

                return $item;
            });
        }

        return view('admin.susunrkp', compact(
            'tahunList',
            'tahun',
            'details',
            'dusunList'
        ));
    }

    public function getKegiatanByTahunDusun(Request $request)
    {
        try {
            $tahun = $request->tahun;
            $dusunId = $request->dusun_id;

            if (!$tahun || !$dusunId) {
                return response()->json([]);
            }

            $tahunSekarang = date('Y');

            $periode = RpjmdesPeriode::where('is_ditetapkan', true)
                ->where('tahun_mulai', '<=', $tahunSekarang)
                ->where('tahun_selesai', '>=', $tahunSekarang)
                ->first();

            if (!$periode) return response()->json([]);

            $data = RpjmdesDetail::with(['kegiatan', 'usulan.rtrw'])
                ->where('rpjmdes_id', $periode->id)
                ->whereHas('usulan.rtrw', function ($q) use ($dusunId) {
                    $q->where('dusun_id', $dusunId);
                })
                ->get()
                ->filter(function ($item) use ($tahun) {

                    $volumeRaw = strtolower(trim($item->usulan->volume ?? '0'));

                    if (str_contains($volumeRaw, 'x')) {
                        $dim = explode('x', $volumeRaw);
                        $total = array_product($dim);
                    } else {
                        $total = (float) preg_replace('/[^0-9.]/', '', $volumeRaw);
                    }

                    $realisasi = MonitoringRkpdes::whereHas('rkpdesDetail', function ($q) use ($item) {
                        $q->where('rpjmdes_detail_id', $item->id);
                    })->sum('volume_realisasi');

                    $sisa = $total - $realisasi;

                    if ($sisa <= 0) {
                        return false;
                    }

                    $sudahDiajukanTahunIni = RkpdesDetail::where('rpjmdes_detail_id', $item->id)
                        ->whereHas('rkpdes', function ($q) use ($tahun) {
                            $q->where('tahun', $tahun);
                        })
                        ->exists();

                    if ($sudahDiajukanTahunIni) {
                        return false;
                    }

                    return true;
                })
                ->map(function ($item) {

                    $volumeRaw = strtolower(trim($item->usulan->volume ?? '0'));
                    $satuan = $item->usulan->satuan ?? 'unit';

                    if (str_contains($volumeRaw, 'x')) {
                        $dim = explode('x', $volumeRaw);
                        $total = array_product($dim);
                    } else {
                        $total = (float) preg_replace('/[^0-9.]/', '', $volumeRaw);
                    }

                    $realisasi = MonitoringRkpdes::whereHas('rkpdesDetail', function ($q) use ($item) {
                        $q->where('rpjmdes_detail_id', $item->id);
                    })->sum('volume_realisasi');

                    $sisa = max($total - $realisasi, 0);

                    return [
                        'id' => $item->id,
                        'nama_kegiatan' => $item->kegiatan->nama_kegiatan ?? '-',
                        'lokasi' => $item->usulan->lokasi ?? '',
                        'satuan' => $satuan,
                        'total' => $total,
                        'realisasi' => $realisasi,
                        'sisa' => $sisa,
                        'status' => $sisa <= 0 ? 'selesai' : 'proses',
                        'is_multi_year' => count($item->tahun_pelaksanaan ?? []) > 1,

                        'penerima_laki' => $item->usulan->penerima_laki ?? 0,
                        'penerima_perempuan' => $item->usulan->penerima_perempuan ?? 0,
                        'penerima_rtm' => $item->usulan->penerima_rtm ?? 0,
                    ];
                })
                ->values();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun' => 'required',
                'rpjmdes_detail_id' => 'required',
                'volume' => 'required|numeric|min:0',
                'lokasi' => 'required'
            ]);

            $rpjm = RpjmdesDetail::findOrFail($request->rpjmdes_detail_id);

            $dim = explode('x', $rpjm->usulan->volume);

            if (count($dim) == 3) $total = $dim[0] * $dim[1] * $dim[2];
            elseif (count($dim) == 2) $total = $dim[0] * $dim[1];
            else $total = $dim[0];

            $terpakai = MonitoringRkpdes::whereHas('rkpdesDetail', function ($q) use ($rpjm) {
                $q->where('rpjmdes_detail_id', $rpjm->id);
            })->sum('volume_realisasi');

            $sisa = $total - $terpakai;

            if ($request->volume > $sisa) {
                return back()->withErrors([
                    'volume' => 'Volume melebihi sisa yang tersedia'
                ]);
            }

            $rkpdes = Rkpdes::firstOrCreate([
                'tahun' => $request->tahun
            ]);

            RkpdesDetail::create([
                'rkpdes_id' => $rkpdes->id,
                'rpjmdes_detail_id' => $rpjm->id,
                'user_id' => \Auth::id(),
                'bidang_id' => $rpjm->bidang_id,
                'sub_bidang_id' => $rpjm->sub_bidang_id,
                'kegiatan_id' => $rpjm->kegiatan_id,

                'lokasi' => $request->lokasi,
                'volume' => $request->volume,
                'satuan' => $rpjm->usulan->satuan ?? 'unit',

                'penerima_laki' => $request->penerima_laki ?? 0,
                'penerima_perempuan' => $request->penerima_perempuan ?? 0,
                'penerima_rtm' => $request->penerima_rtm ?? 0,
                'status' => 'diajukan'
            ]);

            return back()->with('success', 'Berhasil tambah kegiatan');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function update(Request $request, ?int $id)
    {
        $item = RkpdesDetail::findOrFail($id);

        $request->validate([
            'volume' => 'required|numeric|min:0',
            'satuan' => 'required|string'
        ]);

        $bulanMap = [
            'Januari' => 1,
            'Februari' => 2,
            'Maret' => 3,
            'April' => 4,
            'Mei' => 5,
            'Juni' => 6,
            'Juli' => 7,
            'Agustus' => 8,
            'September' => 9,
            'Oktober' => 10,
            'November' => 11,
            'Desember' => 12,
        ];

        $tahunRkpdes = (int) ($item->rkpdes->tahun ?? date('Y'));
        $tahunSekarang = (int) date('Y');
        $bulanSekarang = (int) date('n');

        $waktu = trim($request->waktu_pelaksanaan ?? '');

        $bulanAwal = null;

        if ($waktu !== '') {
            $parts = preg_split('/\s*-\s*/', $waktu);

            $bulanAwalText = trim($parts[0] ?? '');
            $bulanAwal = $bulanMap[$bulanAwalText] ?? null;
        }

        if (
            $tahunRkpdes === $tahunSekarang &&
            $bulanAwal &&
            $bulanAwal < $bulanSekarang
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bulan awal waktu pelaksanaan tidak boleh kurang dari bulan sekarang'
            ], 422);
        }

        $item->update([
            'sdgs' => $request->sdgs,
            'data_existing' => $request->data_existing,
            'target_capaian' => $request->target_capaian,
            'lokasi' => $request->lokasi,
            'volume' => (float) $request->volume,
            'satuan' => $request->satuan,
            'anggaran' => $request->anggaran,
            'sumber_dana' => $request->sumber_dana,
            'penerima_laki' => $request->penerima_laki,
            'penerima_perempuan' => $request->penerima_perempuan,
            'penerima_rtm' => $request->penerima_rtm,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'pelaksana_kegiatan' => $request->pelaksana_kegiatan,
            'rencana_tpk' => $request->rencana_tpk,
            'pola_pelaksanaan' => $request->pola_pelaksanaan,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $item = RkpdesDetail::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function tetapkan(Request $request)
    {
        try {
            $request->validate([
                'tahun' => 'required'
            ]);

            $tahunBaru = $request->tahun;

            $rkpdes = Rkpdes::where('tahun', $tahunBaru)
                ->where('is_ditetapkan', 0)
                ->first();

            if (!$rkpdes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada RKPDes aktif'
                ], 400);
            }

            $detailsBaru = RkpdesDetail::where('rkpdes_id', $rkpdes->id)->get();

            foreach ($detailsBaru as $baru) {

                // set status pengajuan
                $baru->update([
                    'status' => 'disetujui'
                ]);

                $rpjmId = $baru->rpjmdes_detail_id;

                $detailsLama = RkpdesDetail::where('rpjmdes_detail_id', $rpjmId)
                    ->whereHas('rkpdes', function ($q) use ($tahunBaru) {
                        $q->where('tahun', '<', $tahunBaru);
                    })
                    ->get();

                foreach ($detailsLama as $lama) {

                    $realisasi = MonitoringRkpdes::where('rkpdes_detail_id', $lama->id)
                        ->sum('volume_realisasi');

                    $target = (float) $lama->volume;

                    $lanjut = $detailsBaru->contains(function ($item) use ($rpjmId) {
                        return $item->rpjmdes_detail_id == $rpjmId;
                    });

                    if ($realisasi <= 0) {
                        $status = 'baru';
                    } elseif ($realisasi < $target && $lanjut) {
                        $status = 'lanjutan';
                    } elseif ($realisasi < $target) {
                        $status = 'diproses';
                    } else {
                        $status = 'selesai';
                    }
                    $lama->update([
                        'status_progres' => $status
                    ]);
                }
            }

            $rkpdes->update(['is_ditetapkan' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Semua kegiatan berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function parseVolume(string $input)
    {
        preg_match_all('/\d+(\.\d+)?/', $input, $matches);
        $numbers = $matches[0] ?? [];

        if (str_contains($input, 'x') || str_contains($input, 'X')) {
            $volume = count($numbers) ? array_product($numbers) : 0;
        } else {
            $volume = $numbers[0] ?? 0;
        }

        preg_match('/[a-zA-Z%]+/', $input, $satuanMatch);
        $satuan = $satuanMatch[0] ?? null;

        return [
            'volume' => (float)$volume,
            'satuan' => $satuan
        ];
    }

    public function export(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'dusun_id' => 'nullable|exists:dusun,id'
        ]);

        return Excel::download(
            new SusunRkpdesExport(
                (int) $request->tahun,
                $request->dusun_id ? (int) $request->dusun_id : null
            ),
            'rkpdes.xlsx'
        );
    }
}
