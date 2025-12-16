<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $superadmin = User::where('email', 'superadmin@gmail.com')->first();

        if (!$superadmin) {
            // Create superadmin user
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('superadmin'), // Change this password after first login!
                'role_id' => 1, // Assuming 1 is superadmin role
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('✅ Super Admin created successfully!');
            $this->command->info('   Email: superadmin@gmail.com');
            $this->command->info('   Password: superadmin');
            $this->command->warn('⚠️  IMPORTANT: Change this password after first login!');
        } else {
            $this->command->info('ℹ️  Super Admin already exists (ID: ' . $superadmin->id . ')');
            $this->command->info('   Email: ' . $superadmin->email);
            $this->command->info('   Role ID: ' . $superadmin->role_id);
        }
    }
}
