<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\GuestCategorySeeder;
use FerryScheduleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // IMPORTANT: Order matters! Seed in dependency order
        $this->call(UserSeeder::class);              // Must be first (users.id needed by branches)
        $this->call(CustomerSeeder::class);          // Demo customers for login testing
        $this->call(ItemCategorySeeder::class);      // Categories before items
        $this->call(GuestCategorySeeder::class);     // Guest categories
        $this->call(BranchSeeder::class);            // Branches before ferries
        $this->call(FerryBoatsTableSeeder::class);   // Ferries depend on branches
        $this->call(ItemRatesSeeder::class);         // Item rates depend on branches
        $this->call(FerryScheduleSeeder::class);     // Schedules last
    }
}
