<?php

namespace App\Http\Controllers\Kadus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RpjmdesDetail;
use App\Models\RpjmdesPeriode;
use App\Models\Kadus\Rkpdes;
use App\Models\Kadus\RkpdesDetail;
use App\Models\Admin\MonitoringRkpdes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PengajuanRkpController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahunSekarang = date('Y');

        $periode = RpjmdesPeriode::where('is_ditetapkan', true)
            ->where('tahun_mulai', '<=', $tahunSekarang)
            ->where('tahun_selesai', '>=', $tahunSekarang)
            ->first();

        $tahunList = collect();

        if ($periode) {

            $semuaTahun = RpjmdesDetail::where('rpjmdes_id', $periode->id)
                ->pluck('tahun_pelaksanaan')
                ->map(function ($item) {
                    return is_array($item)
                        ? $item
                        : (is_string($item) ? json_decode($item, true) : []);
                })
                ->flatten()
                ->unique()
                ->sort()
                ->values();

            $tahunSudahDitetapkan = Rkpdes::where('is_ditetapkan', 1)
                ->pluck('tahun')
                ->toArray();

            $tahunList = $semuaTahun
                ->reject(function ($tahun) use ($tahunSudahDitetapkan) {
                    return in_array($tahun, $tahunSudahDitetapkan);
                })
                ->values();
        }

        $entries = request('entries', 10);

        $draft = RkpdesDetail::with(['kegiatan', 'rpjmdesDetail.usulan.rtrw.dusun'])
            ->where('user_id', $user->id)
            ->where('status', 'draft')
            ->paginate($entries);

        return view('kadus.pengajuanrkp', compact('tahunList', 'user', 'draft'));
    }

    // AMBIL KEGIATAN BERDASARKAN TAHUN
    public function getKegiatanByTahun(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->dusun_id) {
                return response()->json([]);
            }

            $tahun = $request->tahun;
            $tahunSekarang = date('Y');

            $periode = RpjmdesPeriode::where('is_ditetapkan', true)
                ->where('tahun_mulai', '<=', $tahunSekarang)
                ->where('tahun_selesai', '>=', $tahunSekarang)
                ->first();

            if (!$periode) return response()->json([]);

            $data = RpjmdesDetail::with(['kegiatan', 'usulan.rtrw'])
                ->where('rpjmdes_id', $periode->id)
                ->whereHas('usulan.rtrw', function ($q) use ($user) {
                    $q->where('dusun_id', $user->dusun_id);
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
                        'volume' => $volumeRaw,
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
            Log::error("ERROR kegiatan-by-tahun: " . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // SIMPAN PENGAJUAN RKP
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tahun' => 'required',
                'rpjmdes_detail_id' => 'required|exists:rpjmdes_detail,id',
                'panjang' => 'required|numeric',
                'lebar' => 'nullable|numeric',
                'tinggi' => 'nullable|numeric',
                'lokasi' => 'required'
            ]);

            $rpjm = RpjmdesDetail::findOrFail($request->rpjmdes_detail_id);

            // HITUNG VOLUME INPUT
            $panjang = $request->panjang;
            $lebar = $request->lebar;
            $tinggi = $request->tinggi;
            $satuan = $rpjm->usulan->satuan ?? 'unit';

            if ($panjang && $lebar && $tinggi) {
                $volume = $panjang * $lebar * $tinggi;
            } elseif ($panjang && $lebar) {
                $volume = $panjang * $lebar;
            } else {
                $volume = $panjang;
            }

            $dim = explode('x', $rpjm->usulan->volume);

            if (count($dim) == 3) {
                $total = $dim[0] * $dim[1] * $dim[2];
            } elseif (count($dim) == 2) {
                $total = $dim[0] * $dim[1];
            } else {
                $total = $dim[0];
            }

            $terpakai = MonitoringRkpdes::whereHas('rkpdesDetail', function ($q) use ($rpjm) {
                $q->where('rpjmdes_detail_id', $rpjm->id);
            })->sum('volume_realisasi');

            $sisa = $total - $terpakai;

            if ($volume > $sisa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Volume melebihi sisa yang tersedia'
                ], 422);
            }

            $rkpdes = Rkpdes::firstOrCreate(['tahun' => $request->tahun]);

            $data = RkpdesDetail::create([
                'rkpdes_id' => $rkpdes->id,
                'rpjmdes_detail_id' => $rpjm->id,
                'user_id' => Auth::id(),
                'bidang_id' => $rpjm->bidang_id,
                'sub_bidang_id' => $rpjm->sub_bidang_id,
                'kegiatan_id' => $rpjm->kegiatan_id,
                'lokasi' => $request->lokasi,
                'volume' => $volume,
                'satuan' => $satuan,
                'penerima_laki' => $request->lk ?? 0,
                'penerima_perempuan' => $request->pr ?? 0,
                'penerima_rtm' => $request->rtm ?? 0,
                'status' => 'draft'
            ]);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // UPDATE PENGAJUAN
    public function update(Request $request, ?int $id)
    {
        try {
            $request->validate([
                'panjang' => 'required',
                'lebar' => 'nullable|numeric',
                'tinggi' => 'nullable|numeric',
                'lokasi' => 'required'
            ]);

            $data = RkpdesDetail::findOrFail($id);
            $rpjm = $data->rpjmdesDetail;

            // HITUNG VOLUME BARU
            $panjang = $request->panjang;
            $lebar = $request->lebar;
            $tinggi = $request->tinggi;

            $satuan = $rpjm->usulan->satuan ?? 'unit';

            if ($panjang && $lebar && $tinggi) {
                $volume = $panjang * $lebar * $tinggi;
            } elseif ($panjang && $lebar) {
                $volume = $panjang * $lebar;
            } else {
                $volume = $panjang;
            }

            $dim = explode('x', $rpjm->usulan->volume);

            if (count($dim) == 3) {
                $total = $dim[0] * $dim[1] * $dim[2];
            } elseif (count($dim) == 2) {
                $total = $dim[0] * $dim[1];
            } else {
                $total = $dim[0];
            }

            $terpakai = RkpdesDetail::where('rpjmdes_detail_id', $rpjm->id)
                ->where('id', '!=', $id)
                ->sum('volume');

            $sisa = $total - $terpakai;

            if ($volume > $sisa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Volume melebihi sisa yang tersedia'
                ], 422);
            }

            $data->update([
                'volume' => $volume,
                'satuan' => $satuan,
                'lokasi' => $request->lokasi,
                'penerima_laki' => $request->lk ?? 0,
                'penerima_perempuan' => $request->pr ?? 0,
                'penerima_rtm' => $request->rtm ?? 0
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // HAPUS PENGAJUAN
    public function hapus(?int $id)
    {
        $data = RkpdesDetail::find($id);
        if (!$data)
            return response()->json(['success' => false]);

        $data->delete();
        return response()->json(['success' => true]);
    }

    // KIRIM SEMUA DRAFT
    public function kirim()
    {
        RkpdesDetail::where('user_id', Auth::id())
            ->where('status', 'draft')
            ->update(['status' => 'diajukan']);

        return response()->json(['message' => 'Berhasil diajukan']);
    }
}
