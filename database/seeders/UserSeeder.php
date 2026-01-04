<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin account
        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('admin123'),
                'role_id' => 1,
                'branch_id' => null,
                'mobile' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Admin account
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role_id' => 2,
                'branch_id' => null,
                'mobile' => '9898765432',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
