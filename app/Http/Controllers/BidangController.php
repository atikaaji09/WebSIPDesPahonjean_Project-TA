<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BidangImport;

class BidangController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('entries', 10);
        $bidang = Bidang::paginate($perPage)->withQueryString();

        return view('admin.bidang', compact('bidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255'
        ]);

        Bidang::create([
            'nama_bidang' => $request->nama_bidang
        ]);

        return redirect()->route('admin.bidang')
            ->with('success', 'Bidang berhasil ditambahkan');
    }

    public function update(Request $request, ?int $id)
    {
        $request->validate([
            'nama_bidang' => 'required'
        ]);

        $bidang = Bidang::findOrFail($id);

        $bidang->update([
            'nama_bidang' => $request->nama_bidang
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $bidang = Bidang::findOrFail($id);
        $bidang->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new BidangImport, $request->file('file'));

        return redirect()->route('admin.bidang')
            ->with('success', 'Data bidang berhasil diimport');
    }
}
