<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\SubBidang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KegiatanImport;

class KegiatanController extends Controller
{

    public function index(?int $sub_bidang_id)
    {
        $subbidang = SubBidang::findOrFail($sub_bidang_id);
        $perPage = request('entries', 10);

        $kegiatan = Kegiatan::where('sub_bidang_id', $sub_bidang_id)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.kegiatan', compact('subbidang', 'kegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_bidang_id' => 'required',
            'nama_kegiatan' => 'required'
        ]);

        Kegiatan::create([
            'sub_bidang_id' => $request->sub_bidang_id,
            'nama_kegiatan' => $request->nama_kegiatan
        ]);

        return back();
    }

    public function update(Request $request, ?int $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required'
        ]);

        $kegiatan = Kegiatan::findOrFail($id);

        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request, ?int $sub_bidang_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new KegiatanImport($sub_bidang_id), $request->file('file'));

        return back()->with('success', 'Data kegiatan berhasil diimport');
    }
}
