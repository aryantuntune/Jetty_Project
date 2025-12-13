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

       $this->call(ItemCategorySeeder::class);
       $this->call([GuestCategorySeeder::class]);
       $this->call(BranchSeeder::class); // Branches must come before FerryBoats (foreign key)
       $this->call([
            FerryBoatsTableSeeder::class,
        ]);
       $this->call(ItemRatesSeeder::class); // Add ItemRatesSeeder
       $this->call(RoutesSeeder::class); // Add RoutesSeeder

    

    }
}
