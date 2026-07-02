<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usulan;

class StatusUsulanController extends Controller
{
    public function form()
    {
        return view('warga.cek-status-usulan');
    }

    public function cek(Request $request)
    {
        $request->validate([
            'kode_usulan' => 'required'
        ]);

        $usulan = Usulan::where('no_usulan', $request->kode_usulan)
            ->with('rtrw.dusun')
            ->first();

        if (!$usulan) {
            return back()->with('error', 'Nomor usulan tidak ditemukan');
        }

        return view('warga.data-usulan', compact('usulan'));
    }
}
