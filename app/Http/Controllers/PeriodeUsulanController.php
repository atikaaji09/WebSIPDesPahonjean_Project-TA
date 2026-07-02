<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeriodeUsulan;
use Carbon\Carbon;

class PeriodeUsulanController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->entries ?? 10;

        $periode = PeriodeUsulan::latest()->paginate($entries);

        return view('admin.periode', compact('periode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required'
        ]);

        PeriodeUsulan::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->format('Y-m-d H:i:s'),
            'tanggal_selesai' => Carbon::parse($request->tanggal_selesai)->format('Y-m-d H:i:s'),
            'is_active' => $request->status == 'Aktif'
        ]);

        return back()->with('success', 'Periode berhasil ditambahkan');
    }
}
