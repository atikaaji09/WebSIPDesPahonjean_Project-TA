<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsetDesa;
use App\Imports\AsetImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsetDesaExport;

class AsetDesaController extends Controller
{
    public function index(Request $request)
    {
        $klasAset = $request->get('klas_aset');
        $entries = $request->entries ?? 10;

        $klasAsetList = AsetDesa::select('klas_aset')
            ->distinct()
            ->orderBy('klas_aset')
            ->pluck('klas_aset');

        $query = AsetDesa::orderBy('id', 'desc');

        if (!empty($klasAset)) {
            $query->where('klas_aset', $klasAset);
        }

        $asets = $query->paginate($entries)->withQueryString();

        return view('admin.asetdesa', compact('asets', 'klasAset', 'klasAsetList'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'nilai_perolehan' => str_replace('.', '', $request->nilai_perolehan),
            'luas' => $request->luas ? str_replace(',', '.', $request->luas) : null,
        ]);

        $validated = $request->validate([
            'klas_aset' => 'required|string|max:255',
            'nama_aset' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:100',
            'nilai_perolehan' => 'required|numeric',
            'jenis_kepemilikan' => 'nullable|string|max:255',
            'nomor_kepemilikan' => 'nullable|string|max:255',
            'tanggal_kepemilikan' => 'nullable|date',
            'tahun_perolehan' => 'nullable|integer',
            'kondisi_aset' => 'nullable|string|max:255',
            'luas' => 'nullable|numeric',
            'bukti_kepemilikan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'titik_pangkal' => 'nullable|string|max:255',
            'titik_ujung' => 'nullable|string|max:255',
        ]);

        AsetDesa::create($validated);
        return redirect()->route('admin.asetdesa')->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, ?int $id)
    {
        $aset = AsetDesa::findOrFail($id);

        $request->merge([
            'nilai_perolehan' => str_replace('.', '', $request->nilai_perolehan),
            'luas' => $request->luas ? str_replace(',', '.', $request->luas) : null,
        ]);

        $validated = $request->validate([
            'klas_aset' => 'required|string|max:255',
            'nama_aset' => 'required|string|max:255',
            'kode_aset' => 'required|string|max:100',
            'nilai_perolehan' => 'required|numeric',
            'jenis_kepemilikan' => 'nullable|string|max:255',
            'nomor_kepemilikan' => 'nullable|string|max:255',
            'tanggal_kepemilikan' => 'nullable|date',
            'tahun_perolehan' => 'nullable|integer',
            'kondisi_aset' => 'nullable|string|max:255',
            'luas' => 'nullable|numeric',
            'bukti_kepemilikan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'titik_pangkal' => 'nullable|string|max:255',
            'titik_ujung' => 'nullable|string|max:255',
        ]);

        $aset->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $aset = AsetDesa::findOrFail($id);
        $aset->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            Excel::import(new AsetImport, $request->file('file'));

            return redirect()->route('admin.asetdesa')
                ->with('success', 'Data aset berhasil diimport');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $klasAset = $request->klas_aset;

        return Excel::download(
            new AsetDesaExport($klasAset),
            'laporan-aset-desa.xlsx'
        );
    }
}
