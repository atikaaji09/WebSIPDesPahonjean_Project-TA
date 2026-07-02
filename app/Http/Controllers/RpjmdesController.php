<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RpjmdesDetail;
use App\Models\Bidang;
use App\Models\SubBidang;
use App\Models\Kegiatan;
use App\Models\RpjmdesPeriode;
use App\Models\Usulan;
use App\Models\Dusun;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RpjmdesExport;
use App\Exports\SusunRpjmdesExport;

class RpjmdesController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->input('entries', 10);

        $periodeList = RpjmdesPeriode::where('is_ditetapkan', false)
            ->latest()
            ->get();
        $data = RpjmdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        $periode = null;

        if ($request->filled('periode_id')) {

            $periode = RpjmdesPeriode::where('id', $request->periode_id)
                ->where('is_ditetapkan', false)
                ->first();

            if (!$periode) {
                return redirect()
                    ->route('admin.rpjmdes.index')
                    ->with('error', 'Periode tidak valid / sudah ditetapkan');
            }

            $data = RpjmdesDetail::with('usulan.rtrw.dusun')
                ->where('rpjmdes_id', $periode->id)
                ->when($request->filled('dusun_id'), function ($q) use ($request) {
                    $q->whereHas('usulan.rtrw.dusun', function ($q2) use ($request) {
                        $q2->where('id', $request->dusun_id);
                    });
                })
                ->paginate($entries)
                ->withQueryString();
        }

        return view('admin.susunrpjm', [
            'data' => $data,
            'bidang' => Bidang::all(),
            'subbidang' => SubBidang::all(),
            'kegiatan' => Kegiatan::all(),
            'periode' => $periode,
            'periodeList' => $periodeList,
            'dusunList' => Dusun::all()
        ]);
    }
    public function update(Request $request, ?int $id)
    {
        $data = RpjmdesDetail::with('usulan', 'periode')->findOrFail($id);

        if (!$data->periode || $data->periode->is_ditetapkan) {
            return response()->json([
                'success' => false,
                'message' => 'RPJMDes sudah ditetapkan / tidak valid'
            ]);
        }

        $request->validate([
            'bidang_id' => 'nullable|exists:bidang,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidang,id',
            'kegiatan_id' => 'nullable|exists:kegiatan,id',
            'sasaran' => 'nullable|string',
            'waktu' => 'required|array',
            'waktu.*' => 'digits:4',
            'biaya' => 'nullable|numeric',
            'sumber' => 'nullable|string',
            'pola' => 'nullable|string',
            'volume' => [
                'nullable',
                'regex:/^\d+(\.\d+)?(x\d+(\.\d+)?){0,2}$/'
            ]
        ], [
            'volume.regex' => 'Format volume harus seperti: 200x1.5 atau 200x1.5x0.2 (tanpa satuan)'
        ]);

        $data->update([
            'bidang_id' => $request->bidang_id,
            'sub_bidang_id' => $request->sub_bidang_id,
            'kegiatan_id' => $request->kegiatan_id,
            'sasaran_manfaat' => $request->sasaran,
            'tahun_pelaksanaan' => $request->waktu,
            'anggaran' => $request->biaya,
            'sumber' => $request->sumber,
            'pola_pelaksanaan' => $request->pola,
        ]);

        if ($data->usulan) {

            $updateUsulan = [];

            if ($request->filled('volume')) {

                $cleanVolume = strtolower($request->volume);

                $cleanVolume = str_replace([' ', 'm', '²', '³'], '', $cleanVolume);

                $updateUsulan['volume'] = $cleanVolume;
            }
            if ($request->filled('satuan')) {
                $updateUsulan['satuan'] = $request->satuan;
            }

            if ($request->has('lokasi')) $updateUsulan['lokasi'] = $request->lokasi;
            if ($request->has('lk')) $updateUsulan['penerima_laki'] = $request->lk ?: 0;
            if ($request->has('pr')) $updateUsulan['penerima_perempuan'] = $request->pr ?: 0;
            if ($request->has('rtm')) $updateUsulan['penerima_rtm'] = $request->rtm ?: 0;
            if (!empty($updateUsulan)) {
                $data->usulan->update($updateUsulan);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui'
        ]);
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'tahun_mulai' => 'required|digits:4',
            'tahun_selesai' => 'required|digits:4|gt:tahun_mulai',
        ]);

        $exists = RpjmdesPeriode::where('tahun_mulai', $request->tahun_mulai)
            ->where('tahun_selesai', $request->tahun_selesai)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Periode tersebut sudah ada!');
        }

        $periode = RpjmdesPeriode::create([
            'nama_periode' => $request->nama,
            'tahun_mulai' => $request->tahun_mulai,
            'tahun_selesai' => $request->tahun_selesai,
            'is_ditetapkan' => false
        ]);

        $usulan = Usulan::where('status', 'diterima')
            ->whereDoesntHave('rpjmdesDetail', function ($q) {
                $q->whereNotNull('rpjmdes_id');
            })
            ->get();

        foreach ($usulan as $u) {

            $existing = RpjmdesDetail::where('usulan_id', $u->id)->first();

            if ($existing) {
                $existing->update([
                    'rpjmdes_id' => $periode->id
                ]);
            } else {

                RpjmdesDetail::create([
                    'usulan_id' => $u->id,
                    'rpjmdes_id' => $periode->id
                ]);
            }
        }

        return redirect()->back()->with('success', 'Periode berhasil ditambahkan');
    }

    public function destroy(?int $id)
    {
        $detail = RpjmdesDetail::with('periode')->findOrFail($id);

        if ($detail->periode && $detail->periode->is_ditetapkan) {
            return response()->json([
                'success' => false,
                'message' => 'RPJMDes sudah ditetapkan, tidak bisa dihapus'
            ]);
        }

        $detail->delete();

        return response()->json(['success' => true]);
    }

    public function tetapkan(Request $request)
    {
        try {
            $periode = RpjmdesPeriode::where('is_ditetapkan', false)->latest()->first();

            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode aktif'
                ], 400);
            }

            $belumLengkap = RpjmdesDetail::where('rpjmdes_id', $periode->id)
                ->where(function ($q) {
                    $q->whereNull('bidang_id')
                        ->orWhereNull('sub_bidang_id')
                        ->orWhereNull('kegiatan_id')
                        ->orWhereNull('tahun_pelaksanaan')
                        ->orWhere('tahun_pelaksanaan', '[]');
                })
                ->exists();

            if ($belumLengkap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada data RPJMDes yang belum lengkap!'
                ], 400);
            }

            $details = RpjmdesDetail::where('rpjmdes_id', $periode->id)->get();

            foreach ($details as $d) {
                if ($d->usulan) {
                    $d->usulan->update([
                        'status' => 'masuk_rpjmdes'
                    ]);
                }
            }

            $periode->update(['is_ditetapkan' => true]);

            return response()->json([
                'success' => true,
                'message' => 'RPJMDes berhasil ditetapkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function monitoring(Request $request)
    {
        $periodeList = RpjmdesPeriode::where('is_ditetapkan', true)
            ->latest()
            ->get();

        $entries = $request->input('entries', 10);
        $selectedPeriodeId = $request->periode_id;
        if (!$selectedPeriodeId) {

            $latestPeriode = RpjmdesPeriode::where('is_ditetapkan', true)
                ->latest()
                ->first();

            $selectedPeriodeId = $latestPeriode?->id;
        }

        $data = RpjmdesDetail::whereRaw('1=0')
            ->paginate($entries)
            ->withQueryString();

        if ($selectedPeriodeId) {

            $query = RpjmdesDetail::with([
                'usulan.rtrw.dusun',
                'bidang',
                'subBidang',
                'kegiatan',
                'periode',
                'rkpdesDetails.monitoring'
            ])
                ->where('rpjmdes_id', $selectedPeriodeId);

            if ($request->filled('dusun_id')) {

                $query->whereHas('usulan.rtrw.dusun', function ($q) use ($request) {
                    $q->where('id', $request->dusun_id);
                });
            }

            $data = $query
                ->paginate($entries)
                ->withQueryString();

            $data->getCollection()->transform(function ($item) {

                $volume = $item->usulan->volume ?? '0';

                $satuan = $item->usulan->satuan ?? '';

                $terpakai = $item->rkpdesDetails
                    ->flatMap(function ($detail) {
                        return $detail->monitoring;
                    })
                    ->sum('volume_realisasi');

                $isDimensi =
                    in_array($satuan, ['m2', 'm3']) &&
                    str_contains($volume, 'x');

                if ($isDimensi) {

                    $dimensi = explode('x', strtolower($volume));

                    $dimensi = array_map(function ($v) {
                        return (float) $v;
                    }, $dimensi);

                    if (count($dimensi) == 3) {

                        $hasil =
                            $dimensi[0] *
                            $dimensi[1] *
                            $dimensi[2];
                    } elseif (count($dimensi) == 2) {

                        $hasil =
                            $dimensi[0] *
                            $dimensi[1];
                    } else {

                        $hasil = (float) $volume;
                    }
                } else {

                    $hasil = (float) $volume;
                }

                $sisa = max(0, $hasil - $terpakai);

                $jumlahMasukRkpdes = $item->rkpdesDetails->count();

                if ($jumlahMasukRkpdes == 1) {
                    $item->status = 'masuk_rkpdes';
                } elseif ($jumlahMasukRkpdes > 1) {
                    $item->status = 'lanjutan';
                }

                if ($satuan == 'm2') {

                    $satuanFormat = 'm<sup>2</sup>';
                } elseif ($satuan == 'm3') {

                    $satuanFormat = 'm<sup>3</sup>';
                } else {

                    $satuanFormat = $satuan;
                }

                $item->volume_hasil = $hasil;
                $item->volume_asli = $volume;
                $item->is_dimensi = $isDimensi;
                $item->sisa = $sisa;
                $item->volume_terpakai = $terpakai;
                $item->satuan_format = $satuanFormat;

                return $item;
            });
        }

        return view('admin.monitoringrpjm', [
            'periodeList' => $periodeList,
            'data' => $data,
            'dusunList' => Dusun::all(),
            'selectedPeriodeId' => $selectedPeriodeId
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:rpjmdes_periode,id',
            'dusun_id'   => 'nullable|exists:dusun,id',
        ]);

        return Excel::download(
            new RpjmdesExport($request->periode_id, $request->dusun_id),
            'laporan-rpjmdes.xlsx'
        );
    }

    public function destroyPeriode(?int $id)
    {
        $periode = RpjmdesPeriode::findOrFail($id);

        if ($periode->is_ditetapkan) {
            return redirect()->back()
                ->with('error', 'Periode RPJMDes yang sudah ditetapkan tidak bisa dihapus');
        }

        RpjmdesDetail::where('rpjmdes_id', $periode->id)
            ->update([
                'rpjmdes_id' => null
            ]);

        $periode->delete();

        return redirect()
            ->route('admin.rpjmdes.index')
            ->with('success', 'Periode RPJMDes berhasil dihapus');
    }

    public function exportSusun(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:rpjmdes_periode,id',
            'dusun_id'   => 'nullable|exists:dusun,id',
        ]);

        return Excel::download(
            new SusunRpjmdesExport($request->periode_id, $request->dusun_id),
            'penyusunan-rpjmdes.xlsx'
        );
    }
}
