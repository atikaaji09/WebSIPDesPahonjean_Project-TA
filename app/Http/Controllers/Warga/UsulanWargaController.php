<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Usulan;
use App\Models\Rtrw;

class UsulanWargaController extends Controller
{

    public function step1(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'rt_rw_id' => 'required'
        ]);

        session([
            'usulan_step1' => [
                'nama_lengkap' => $request->nama_lengkap,
                'rt_rw_id' => $request->rt_rw_id
            ]
        ]);

        return redirect()->route('form.usulan.data');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gagasan_kegiatan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'volume' => 'required|string',
            'satuan' => 'required|string',
            'penerima_laki' => 'nullable|integer|min:0',
            'penerima_perempuan' => 'nullable|integer|min:0',
            'penerima_rtm' => 'nullable|integer|min:0',
        ]);

        $step1 = session('usulan_step1');

        if (!$step1) {
            return redirect()->route('form.usulan')
                ->with('error', 'Silakan isi data identitas terlebih dahulu');
        }

        $gagasan = ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $request->gagasan_kegiatan))));
        $lokasi = strtoupper(trim(preg_replace('/\s+/', ' ', $request->lokasi)));

        $volume = strtolower(str_replace(' ', '', $request->volume));
        $satuan = $request->satuan;

        if (!preg_match('/^[0-9xX.]+$/', $volume)) {
            return back()
                ->withInput()
                ->with('error', 'Format volume tidak valid (contoh: 5x2 atau 100)');
        }

        $rtrw = Rtrw::findOrFail($step1['rt_rw_id']);
        $dusunId = $rtrw->dusun_id;

        $gagasanCheck = strtolower(str_replace(' ', '', $gagasan));
        $lokasiCheck = strtolower(str_replace(' ', '', $lokasi));

        $exists = Usulan::where('dusun_id', $dusunId)
            ->whereRaw(
                "LOWER(REPLACE(gagasan_kegiatan, ' ', '')) = ?",
                [$gagasanCheck]
            )
            ->whereRaw(
                "LOWER(REPLACE(lokasi, ' ', '')) = ?",
                [$lokasiCheck]
            )
            ->where(function ($q) {
                $q->whereIn('status', ['diajukan', 'diterima'])
                    ->orWhereHas('rpjmdesDetail', function ($q2) {
                        $q2->whereNull('rpjmdes_id')
                            ->orWhereHas('periode', function ($q3) {
                                $q3->where('is_ditetapkan', false);
                            });
                    });
            })
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Usulan dengan gagasan dan lokasi yang sama masih dalam proses pengajuan atau penyusunan RPJMDes');
        }

        $lastId = Usulan::max('id');
        $number = $lastId ? $lastId + 1 : 1;
        $noUsulan = 'USL-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        try {
            Usulan::create([
                'no_usulan' => $noUsulan,
                'nama_lengkap' => $step1['nama_lengkap'],
                'rt_rw_id' => $step1['rt_rw_id'],
                'dusun_id' => $dusunId,
                'gagasan_kegiatan' => $gagasan,
                'lokasi' => $lokasi,
                'volume' => $volume,
                'satuan' => $satuan,
                'penerima_laki' => $request->penerima_laki ?? 0,
                'penerima_perempuan' => $request->penerima_perempuan ?? 0,
                'penerima_rtm' => $request->penerima_rtm ?? 0,
            ]);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi duplikasi data usulan di sistem');
        }

        session()->forget('usulan_step1');

        return redirect()->route('form.usulan')
            ->with('success', 'Usulan berhasil dikirim!');
    }
}
