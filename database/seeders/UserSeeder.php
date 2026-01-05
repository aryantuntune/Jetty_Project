<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin account - using User model to avoid double-hashing
        // (User model has 'password' => 'hashed' cast which auto-hashes)
        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => 'admin123', // Will be auto-hashed by model
                'role_id' => 1,
                'branch_id' => null,
                'mobile' => null,
            ]
        );

        // Admin account
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => 'admin123', // Will be auto-hashed by model
                'role_id' => 2,
                'branch_id' => null,
                'mobile' => '9898765432',
            ]
        );
    }
}
