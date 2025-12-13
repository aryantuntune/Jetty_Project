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
        // Seed in dependency order (foreign keys)
        $this->call(UserSeeder::class);              // Must be first (user_id references)
        $this->call(CustomerSeeder::class);          // Demo customers for bookings
        $this->call(ItemCategorySeeder::class);      // Item categories
        $this->call(GuestCategorySeeder::class);     // Guest categories
        $this->call(BranchSeeder::class);            // Branches (referenced by ferryboats, routes, item_rates)
        $this->call(FerryBoatsTableSeeder::class);   // Ferry boats (needs branches)
        $this->call(ItemRatesSeeder::class);         // Item rates (needs branches, item_categories, users)
        $this->call(RoutesSeeder::class);            // Routes (needs branches)
        $this->call(FerryScheduleSeeder::class);     // Ferry schedules
    }
}
