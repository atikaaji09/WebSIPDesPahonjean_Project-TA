<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubBidang;
use App\Models\Bidang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubBidangImport;

class SubBidangController extends Controller
{

    public function index(?int $bidang_id)
    {
        $bidang = Bidang::findOrFail($bidang_id);

        $subbidang = SubBidang::where('bidang_id', $bidang_id)
            ->paginate(request('entries', 10));

        return view('admin.subbidang', compact('bidang', 'subbidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required',
            'nama_sub_bidang' => 'required'
        ]);

        SubBidang::create([
            'bidang_id' => $request->bidang_id,
            'nama_sub_bidang' => $request->nama_sub_bidang
        ]);

        return back();
    }

    public function update(Request $request, ?int $id)
    {
        $request->validate([
            'nama_sub_bidang' => 'required'
        ]);

        $subbidang = SubBidang::findOrFail($id);

        $subbidang->update([
            'nama_sub_bidang' => $request->nama_sub_bidang
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $subbidang = SubBidang::findOrFail($id);
        $subbidang->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request, ?int $bidang_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new SubBidangImport($bidang_id), $request->file('file'));

        return back()->with('success', 'Data Sub Bidang berhasil diimport');
    }
}
