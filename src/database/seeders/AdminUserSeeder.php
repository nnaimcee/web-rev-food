<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $exists = DB::table('users')->where('username', 'admin')->exists();
        if (!$exists) {
            DB::table('users')->insert([
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'avatar_img' => null,
                'created_at' => now(),
            ]);
        }
    }
}

