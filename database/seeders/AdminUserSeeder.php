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
            ['username' => 'admin123'],
            [
                'name' => 'Admin',
                'dusun_id' => null,
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin321'),
            ]
        );
    }
}
