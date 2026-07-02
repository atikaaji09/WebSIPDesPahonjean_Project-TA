<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => 1
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'role' => $user->role,
                    'redirect' => $user->role == 'admin' ? route('admin.home') : route('kadus.home')
                ]);
            }

            return $user->role == 'admin'
                ? redirect()->route('admin.home')
                : redirect()->route('kadus.home');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah / akun tidak aktif'
            ], 422);
        }

        return back()->with('error', 'Username atau password salah / akun tidak aktif');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('login')]);
        }

        return redirect()->route('login');
    }
}
