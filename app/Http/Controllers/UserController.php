<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required|in:admin,kadus',
        ]);

        if ($request->role == 'kadus' && !$request->dusun_id) {
            return back()->with('error', 'Kadus wajib pilih dusun');
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'dusun_id' => $request->role == 'admin' ? null : $request->dusun_id,
            'is_active' => $request->is_active
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function toggleStatus(Request $request, ?int $id)
    {
        $user = User::findOrFail($id);

        $user->is_active = $request->input('is_active');
        $user->save();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, ?int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'username' => "required|unique:users,username,{$id}",
            'role' => 'required|in:admin,kadus'
        ]);

        if ($request->role == 'kadus' && !$request->dusun_id) {
            return response()->json(['success' => false, 'message' => 'Kadus wajib pilih dusun']);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->dusun_id = $request->role == 'admin' ? null : $request->dusun_id;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function destroy(?int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}
