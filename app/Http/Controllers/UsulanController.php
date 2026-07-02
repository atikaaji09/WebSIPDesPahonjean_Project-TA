<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usulan;
use App\Models\Rtrw;
use App\Models\Dusun;
use App\Models\RpjmdesDetail;
use App\Models\RpjmdesPeriode;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsulanExport;

class UsulanController extends Controller
{

    public function index(Request $request)
    {
        $entries = $request->input('entries', 10);
        $dusunId = $request->dusun_id;

        $query = Usulan::with('rtrw')
            ->where('status', 'diajukan');

        if ($request->filled('dusun_id')) {
            $query->whereHas('rtrw', function ($q) use ($dusunId) {
                $q->where('dusun_id', $dusunId);
            });
        }

        $usulan = $query->paginate($entries)->withQueryString();
        $dusun = Dusun::all();
        $lastId = Usulan::max('id');
        $number = $lastId ? $lastId + 1 : 1;
        $noUsulan = 'USL-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return view('admin.kelolausulan', compact('usulan', 'dusun', 'dusunId', 'noUsulan'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'dusun_id' => 'required|exists:dusun,id',
            'rt_rw_id' => 'required|exists:rt_rw,id',
            'gagasan_kegiatan' => 'required|string',
            'lokasi' => 'required|string',
            'volume' => [
                'required',
                'regex:/^\d+(\.\d+)?(x\d+(\.\d+)?){0,2}$/'
            ]
        ], [
            'volume.regex' => 'Format volume harus seperti: 200x1.5 atau 200x1.5x0.2'
        ]);

        $volume = strtolower(str_replace(' ', '', $request->volume));
        $gagasan = ucwords(strtolower(trim($request->gagasan_kegiatan)));
        $lokasi = strtoupper(trim($request->lokasi));

        $lastId = Usulan::max('id');
        $number = $lastId ? $lastId + 1 : 1;

        $noUsulan = 'USL-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        Usulan::create([
            'no_usulan' => $noUsulan,
            'nama_lengkap' => $request->nama,
            'rt_rw_id' => $request->rt_rw_id,
            'dusun_id' => $request->dusun_id,
            'gagasan_kegiatan' => $gagasan,
            'lokasi' => $lokasi,
            'volume' => $volume,
            'satuan' => $request->satuan,
            'penerima_laki' => $request->lk ?? 0,
            'penerima_perempuan' => $request->pr ?? 0,
            'penerima_rtm' => $request->rtm ?? 0,
        ]);

        return redirect()->route('admin.kelolausulan')
            ->with('success', 'Usulan berhasil ditambahkan');
    }

    public function update(Request $request, ?int $id)
    {
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'rt_rw_id' => 'required|exists:rt_rw,id',
                'gagasan_kegiatan' => 'required|string',
                'lokasi' => 'required|string',
                'volume' => [
                    'required',
                    'regex:/^\d+(\.\d+)?(x\d+(\.\d+)?){0,2}$/'
                ],
                'satuan' => 'required|string',
                'penerima_laki' => 'nullable|integer',
                'penerima_perempuan' => 'nullable|integer',
                'penerima_rtm' => 'nullable|integer',
            ]);

            $validated['volume'] = strtolower(str_replace(' ', '', $validated['volume']));
            $validated['penerima_laki'] = $validated['penerima_laki'] ?? 0;
            $validated['penerima_perempuan'] = $validated['penerima_perempuan'] ?? 0;
            $validated['penerima_rtm'] = $validated['penerima_rtm'] ?? 0;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }

        $usulan = Usulan::findOrFail($id);
        $usulan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Usulan berhasil diubah'
        ]);
    }

    public function destroy(?int $id)
    {
        $usulan = Usulan::findOrFail($id);
        $usulan->delete();

        return response()->json(['success' => true]);
    }

    public function approve(?int $id)
    {
        $usulan = Usulan::findOrFail($id);

        $usulan->update([
            'status' => 'diterima'
        ]);

        $periode = RpjmdesPeriode::where('is_ditetapkan', false)
            ->latest()
            ->first();

        $rpjmdesId = $periode ? $periode->id : null;

        $exists = RpjmdesDetail::where('usulan_id', $usulan->id)->exists();

        if (!$exists) {
            RpjmdesDetail::create([
                'usulan_id' => $usulan->id,
                'rpjmdes_id' => $rpjmdesId
            ]);
        } elseif ($periode) {
            RpjmdesDetail::where('usulan_id', $usulan->id)
                ->whereNull('rpjmdes_id')
                ->update([
                    'rpjmdes_id' => $periode->id
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function reject(?int $id)
    {
        $usulan = Usulan::findOrFail($id);

        $usulan->update([
            'status' => 'ditolak'
        ]);

        return response()->json(['success' => true]);
    }

    public function approveAll()
    {
        $usulans = Usulan::where('status', 'diajukan')->get();

        $periode = RpjmdesPeriode::where('is_ditetapkan', false)
            ->latest()
            ->first();

        $rpjmdesId = $periode ? $periode->id : null;

        foreach ($usulans as $usulan) {

            $usulan->update([
                'status' => 'diterima'
            ]);

            $exists = RpjmdesDetail::where('usulan_id', $usulan->id)->exists();

            if (!$exists) {
                RpjmdesDetail::create([
                    'usulan_id' => $usulan->id,
                    'rpjmdes_id' => $rpjmdesId
                ]);
            } elseif ($periode) {
                RpjmdesDetail::where('usulan_id', $usulan->id)
                    ->whereNull('rpjmdes_id')
                    ->update([
                        'rpjmdes_id' => $periode->id
                    ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        $dusunId = $request->dusun_id;

        return Excel::download(
            new UsulanExport($dusunId),
            'data-usulan.xlsx'
        );
    }
}
