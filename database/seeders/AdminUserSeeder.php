<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'], // kunci unik
            [
                'name' => 'Admin',
                'dusun_id' => null, // admin tidak perlu dusun
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
