<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DusunImport;

class DusunController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->entries ?? 10;

        $dusun = Dusun::withCount('rtrw')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.dusun', compact('dusun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dusun' => 'required'
        ]);

        Dusun::create([
            'nama_dusun' => $request->nama_dusun
        ]);

        return back()->with('success', 'Dusun berhasil ditambahkan');
    }

    public function update(Request $request, ?int $id)
    {
        $request->validate([
            'nama_dusun' => 'required'
        ]);

        $dusun = Dusun::findOrFail($id);

        $dusun->update([
            'nama_dusun' => $request->nama_dusun
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $dusun = Dusun::findOrFail($id);
        $dusun->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new DusunImport, $request->file('file'));

        return back()->with('success', 'Data dusun berhasil diimport');
    }
}
