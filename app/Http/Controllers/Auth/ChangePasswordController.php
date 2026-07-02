<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function index()
    {
        // 🔒 Batasi hanya admin & kadus
        if (!in_array(Auth::user()->role, ['admin', 'kadus'])) {
            abort(403);
        }

        return view('auth.ubah-password');
    }

    public function update(Request $request)
    {
        // 🔒 Batasi juga di proses update (WAJIB biar aman)
        if (!in_array(Auth::user()->role, ['admin', 'kadus'])) {
            abort(403);
        }

        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_baru.min'       => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_lama.required'  => 'Password lama wajib diisi.',
            'password_baru.required'  => 'Password baru wajib diisi.',
        ]);

        $user = Auth::user();

        // cek password lama
        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan salah.');
        }

        // update password
        $user->password = Hash::make($request->password_baru);
        $user->save();

        // redirect ke home berdasarkan role
        $homeRoute = ($user->role === 'admin') ? '/admin/home' : '/kadus/home';

        return redirect($homeRoute)->with('success', 'Password berhasil diubah!');
    }
}
