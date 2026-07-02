<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dusun;
use App\Models\User;

class AdminController extends Controller
{
    public function pengguna(Request $request)
    {
        $entries = $request->get('entries', 10);

        $dusun = Dusun::all();

        $users = User::with('dusun')
            ->paginate($entries)
            ->withQueryString();

        return view('admin.pengguna', compact('dusun', 'users'));
    }
}
