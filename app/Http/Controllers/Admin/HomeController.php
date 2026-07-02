<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usulan;
use App\Models\Admin\RkpdesDetail;
use App\Models\RpjmdesDetail;

class HomeController extends Controller
{
    public function home()
    {
        $totalUsulan = Usulan::count();

        $diproses = RkpdesDetail::where('status_progres', 'diproses')->count();
        $selesai = RkpdesDetail::where('status_progres', 'selesai')->count();
        $lanjutan = RpjmdesDetail::where('status', 'lanjutan')->count();

        return view('admin.home', compact(
            'totalUsulan',
            'diproses',
            'selesai',
            'lanjutan'
        ));
    }
}
