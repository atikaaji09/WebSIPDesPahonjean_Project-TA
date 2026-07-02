<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\Rtrw;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RtrwImport;

class RtrwController extends Controller
{
    public function index(Request $request, ?int $id)
    {
        $dusun = Dusun::findOrFail($id);

        $perPage = $request->entries ?? 10;

        $rtrw = Rtrw::where('dusun_id', $id)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.rtrw', compact('dusun', 'rtrw'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dusun_id' => 'required|exists:dusun,id',
            'rt' => 'required',
            'rw' => 'required'
        ]);

        Rtrw::create([
            'dusun_id' => $request->dusun_id,
            'rt' => $request->rt,
            'rw' => $request->rw
        ]);

        return back()->with('success', 'RT/RW berhasil ditambahkan');
    }

    public function update(Request $request, ?int $id)
    {
        $request->validate([
            'rt' => 'required',
            'rw' => 'required'
        ]);

        $rtrw = Rtrw::findOrFail($id);

        $rtrw->update([
            'rt' => $request->rt,
            'rw' => $request->rw
        ]);

        return response()->json(['success' => true]);
    }

    public function getByDusun(?int $id)
    {
        $rtrw = Rtrw::where('dusun_id', $id)->get();
        return response()->json($rtrw);
    }

    public function destroy(?int $id)
    {
        $subbidang = Rtrw::findOrFail($id);
        $subbidang->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request, ?int $dusun_id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new RtrwImport($dusun_id), $request->file('file'));

        return back()->with('success', 'Data RT/RW berhasil diimport');
    }
}
