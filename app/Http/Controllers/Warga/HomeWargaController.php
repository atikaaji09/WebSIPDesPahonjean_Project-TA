<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Usulan;
use App\Models\Admin\RkpdesDetail;

class HomeWargaController extends Controller
{
    public function index()
    {

        $usulanMasuk = Usulan::count();
        $diproses = RkpdesDetail::where('status_progres', 'diproses')->count();
        $diterima = Usulan::where('status', 'masuk_rpjmdes')->count();

        return view('warga.home', compact('usulanMasuk', 'diproses', 'diterima'));
    }
}
