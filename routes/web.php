<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RpjmdesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DusunController;
use App\Http\Controllers\RtrwController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\SubBidangController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PeriodeUsulanController;
use App\Http\Controllers\UsulanController;
use App\Http\Controllers\Kadus\PengajuanRkpController;
use App\Http\Controllers\AsetDesaController;
use App\Http\Controllers\Warga\StatusUsulanController;
use App\Http\Controllers\Admin\SusunRkpController;
use App\Http\Controllers\Admin\MonitoringRkpController;
use App\Http\Controllers\Admin\LaporanTahunanController;
use App\Http\Controllers\Kadus\KadusRpjmdesController;
use App\Http\Controllers\Kadus\KadusRkpdesController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Kadus\KadusHomeController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Warga\HomeWargaController;
use App\Http\Controllers\Warga\UsulanWargaController;
use App\Models\Kegiatan;
use App\Models\SubBidang;
use App\Models\PeriodeUsulan;
use App\Models\Dusun;
use App\Models\Rtrw;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| DEFAULT ROUTE (HALAMAN PERTAMA)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('warga.home');
});

/*
|--------------------------------------------------------------------------
| WARGA ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/warga', [HomeWargaController::class, 'index'])->name('warga.home');

Route::get('/form-usulan', function () {

    $periodeAktif = PeriodeUsulan::where('is_active', true)
        ->where('tanggal_mulai', '<=', now())
        ->where('tanggal_selesai', '>=', now())
        ->first();

    $dusun = Dusun::all();
    $rtrw = Rtrw::all();

    // generate nomor usulan
    $last = \App\Models\Usulan::latest()->first();
    $number = $last ? $last->id + 1 : 1;

    $noUsulan = 'USL-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

    return view('warga.form-usulan', compact(
        'periodeAktif',
        'dusun',
        'rtrw',
        'noUsulan'
    ));
})->name('form.usulan');
Route::post('/form-usulan', [UsulanWargaController::class, 'step1'])
    ->name('form.usulan.step1');

Route::get('/form-data-usulan', function () {

    $periodeAktif = PeriodeUsulan::where('is_active', true)
        ->where('tanggal_mulai', '<=', now())
        ->where('tanggal_selesai', '>=', now())
        ->first();

    return view('warga.form-data-usulan', compact('periodeAktif'));
})->name('form.usulan.data');
Route::post('/form-usulan/kirim', [UsulanWargaController::class, 'store'])
    ->name('form.usulan.store');

Route::get('/status-usulan', [StatusUsulanController::class, 'form'])
    ->name('status.usulan');
Route::get('/status-usulan/cek', [StatusUsulanController::class, 'cek'])
    ->name('status.usulan.cek');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/ubah-password', [ChangePasswordController::class, 'index'])->name('password.form');
    Route::post('/ubah-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/dusun/{id}/rtrw', [RtrwController::class, 'getByDusun']);

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // ================= DASHBOARD =================
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // ================= MASTER DATA =================
    Route::get('/bidang', [BidangController::class, 'index'])->name('bidang');
    Route::post('/bidang/tambah', [BidangController::class, 'store'])->name('bidang.store');
    Route::put('/bidang/edit/{id}', [BidangController::class, 'update'])->name('bidang.update');
    Route::delete('/bidang/hapus/{id}', [BidangController::class, 'destroy'])->name('bidang.destroy');
    Route::post('/bidang/import', [BidangController::class, 'import'])->name('bidang.import');

    Route::get('/subbidang/{bidang_id}', [SubBidangController::class, 'index'])->name('subbidang');
    Route::post('/subbidang/tambah', [SubBidangController::class, 'store'])->name('subbidang.store');
    Route::put('/subbidang/edit/{id}', [SubBidangController::class, 'update'])->name('subbidang.update');
    Route::delete('/subbidang/hapus/{id}', [SubBidangController::class, 'destroy'])->name('subbidang.destroy');
    Route::post('/subbidang/import/{bidang_id}', [SubBidangController::class, 'import'])->name('subbidang.import');

    Route::get('/kegiatan/{sub_bidang_id}', [KegiatanController::class, 'index'])->name('kegiatan');
    Route::post('/kegiatan/tambah', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/edit/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/hapus/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    Route::post('/kegiatan/import/{sub_bidang_id}', [KegiatanController::class, 'import'])->name('kegiatan.import');

    // ================= DUSUN =================
    Route::get('/dusun', [DusunController::class, 'index'])->name('dusun');
    Route::post('/dusun/tambah', [DusunController::class, 'store'])->name('dusun.store');
    Route::put('/dusun/edit/{id}', [DusunController::class, 'update'])->name('dusun.update');
    Route::delete('/dusun/hapus/{id}', [DusunController::class, 'destroy'])->name('dusun.destroy');
    Route::post('/dusun/import', [DusunController::class, 'import'])->name('dusun.import');

    // ================= RTRW =================
    Route::get('/dusun/{id}/rtrw', [RtrwController::class, 'index'])->name('rtrw');
    Route::post('/rtrw/tambah', [RtrwController::class, 'store'])->name('rtrw.store');
    Route::put('/rtrw/edit/{id}', [RtrwController::class, 'update'])->name('rtrw.update');
    Route::delete('/rtrw/hapus/{id}', [RtrwController::class, 'destroy'])->name('rtrw.destroy');
    Route::post('/rtrw/import/{dusun_id}', [RtrwController::class, 'import'])->name('rtrw.import');

    // ================= PERIODE =================
    Route::get('/periode', [PeriodeUsulanController::class, 'index'])->name('periode');
    Route::post('/periode/tambah', [PeriodeUsulanController::class, 'store'])->name('periode.store');

    // ================= KELOLA USULAN =================
    Route::prefix('kelolausulan')->group(function () {
        Route::get('/', [UsulanController::class, 'index'])->name('kelolausulan');
        Route::post('/tambah', [UsulanController::class, 'storeAdmin'])->name('kelolausulan.storeAdmin');
        Route::put('/edit/{id}', [UsulanController::class, 'update'])->name('kelolausulan.update');
        Route::delete('/hapus/{id}', [UsulanController::class, 'destroy'])->name('kelolausulan.destroy');
    });

    Route::post('/usulan/approve/{id}', [UsulanController::class, 'approve']);
    Route::post('/usulan/reject/{id}', [UsulanController::class, 'reject']);
    Route::post('/usulan/approve-all', [UsulanController::class, 'approveAll']);

    // ================= RPJMDes =================
    Route::get('/penyusunanrpjm', [RpjmdesController::class, 'index'])->name('susunrpjm');
    Route::post('/rpjmdes/periode/tambah', [RpjmdesController::class, 'storePeriode']);
    Route::get('/rpjmdes', [RpjmdesController::class, 'index'])->name('rpjmdes.index');
    Route::put('/penyusunanrpjm/update/{id}', [RpjmdesController::class, 'update'])->name('penyusunanrpjm.update');
    Route::delete('/penyusunanrpjm/delete/{id}', [RpjmdesController::class, 'destroy'])->name('penyusunanrpjm.delete');
    Route::delete('/rpjmdes/periode/{id}', [RpjmdesController::class, 'destroyPeriode'])
        ->name('rpjmdes.periode.delete');
    Route::post('/rpjmdes/tetapkan', [RpjmdesController::class, 'tetapkan'])->name('rpjmdes.tetapkan');
    Route::get('/monitoringrpjm', [RpjmdesController::class, 'monitoring'])->name('rpjmdes.monitoring');

    // ================= RKPDes =================
    Route::get('/penyusunanrkp', [SusunRkpController::class, 'index'])->name('susunrkp');
    Route::get('/kegiatan-by-tahun-dusun', [SusunRkpController::class, 'getKegiatanByTahunDusun']);
    Route::post('/penyusunanrkp/tambah', [SusunRkpController::class, 'store'])->name('susunrkp.store');
    Route::put('/susunrkp/update/{id}', [SusunRkpController::class, 'update']);
    Route::post('/susunrkp/tetapkan', [SusunRkpController::class, 'tetapkan'])->name('susunrkp.tetapkan');
    Route::delete('/susunrkp/hapus/{id}', [SusunRkpController::class, 'destroy']);

    // ================= MONITORING =================
    Route::get('/monitoringrkp', [MonitoringRkpController::class, 'index'])->name('monitoringrkp');
    Route::post('/monitoringrkp/progres', [MonitoringRkpController::class, 'storeProgres']);

    // ================= ASET DESA =================
    Route::get('/asetdesa', [AsetDesaController::class, 'index'])->name('asetdesa');
    Route::post('/asetdesa/store', [AsetDesaController::class, 'store'])->name('asetdesa.store');
    Route::post('/asetdesa/import', [AsetDesaController::class, 'import'])->name('asetdesa.import');
    Route::post('/asetdesa/export', [AsetDesaController::class, 'export'])->name('asetdesa.export');
    Route::put('/asetdesa/update/{id}', [AsetDesaController::class, 'update'])->name('asetdesa.update');
    Route::delete('/asetdesa/destroy/{id}', [AsetDesaController::class, 'destroy'])->name('asetdesa.destroy');

    // ================= LAPORAN =================
    Route::get('/laporan', [LaporanTahunanController::class, 'index'])->name('laporan');

    // ================= USER =================
    Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
    Route::post('/pengguna/tambah', [UserController::class, 'store'])->name('pengguna.store');
    Route::post('/pengguna/status/{id}', [UserController::class, 'toggleStatus'])->name('pengguna.status');
    Route::put('/pengguna/{id}', [UserController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/hapus/{id}', [UserController::class, 'destroy'])->name('pengguna.destroy');

    // ================= EXPORT =================
    Route::get('/rpjmdes/export', [RpjmdesController::class, 'export']);
    Route::get('/rkpdes/export', [MonitoringRkpController::class, 'export']);
    Route::get('/laporan/export', [LaporanTahunanController::class, 'export'])->name('laporan.export');
    Route::get('/asetdesa/export', [AsetDesaController::class, 'export'])->name('asetdesa.export');
    Route::get('/susunrpjmdes/export', [RpjmdesController::class, 'exportSusun'])->name('susunrpjmdes.export');
    Route::get('/susunrkpdes/export', [SusunRkpController::class, 'export'])
        ->name('susunrkpdes.export');
    Route::get('/export', [UsulanController::class, 'export'])->name('kelolausulan.export');
});

/*
|--------------------------------------------------------------------------
| KADUS ROUTE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/kadus/home', [KadusHomeController::class, 'index'])
        ->name('kadus.home');
    Route::get('/kadus/rpjmdes', [KadusRpjmdesController::class, 'index'])
        ->name('kadus.rpjmdes');
    Route::get('/kadus/rpjmdes/data', [KadusRpjmdesController::class, 'getData'])
        ->name('kadus.rpjmdes.data');
    Route::get('/kadus/pengajuanrkp', [PengajuanRkpController::class, 'index']);
    Route::post('/kadus/pengajuanrkp/tambah', [PengajuanRkpController::class, 'store']);
    Route::get('/kadus/rpjmdes/kegiatan-by-tahun', [PengajuanRkpController::class, 'getKegiatanByTahun']);
    Route::post('/kadus/pengajuanrkp/store', [PengajuanRkpController::class, 'store']);
    Route::post('/kadus/pengajuanrkp/kirim', [PengajuanRkpController::class, 'kirim']);
    Route::put('/kadus/pengajuanrkp/update/{id}', [PengajuanRkpController::class, 'update']);
    Route::delete('/kadus/pengajuanrkp/hapus/{id}', [PengajuanRkpController::class, 'hapus']);
    Route::get('/kadus/rkpdes', [KadusRkpdesController::class, 'index'])->name('kadus.rkpdes');
});
