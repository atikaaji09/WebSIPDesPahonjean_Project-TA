<?php

namespace App\Http\Controllers\Kadus;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Usulan;
use App\Models\Admin\RkpdesDetail;

class KadusHomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dusunId = $user->dusun_id;

        $totalUsulan = Usulan::whereHas('rtrw', function ($q) use ($dusunId) {
            $q->where('dusun_id', $dusunId);
        })->count();

        $diproses = RkpdesDetail::whereHas('rpjmdesDetail.usulan.rtrw', function ($q) use ($dusunId) {
            $q->where('dusun_id', $dusunId);
        })->where('status_progres', 'diproses')->count();

        $lanjutan = RkpdesDetail::whereHas('rpjmdesDetail', function ($q) use ($dusunId) {
            $q->where('status', 'lanjutan')
                ->whereHas('usulan.rtrw', function ($q2) use ($dusunId) {
                    $q2->where('dusun_id', $dusunId);
                });
        })->count();

        $selesai = RkpdesDetail::whereHas('rpjmdesDetail.usulan.rtrw', function ($q) use ($dusunId) {
            $q->where('dusun_id', $dusunId);
        })->where('status_progres', 'selesai')->count();

        return view('kadus.home', compact(
            'totalUsulan',
            'diproses',
            'lanjutan',
            'selesai'
        ));
    }
}
