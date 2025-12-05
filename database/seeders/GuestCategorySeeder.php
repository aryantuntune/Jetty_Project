<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['id' => 1, 'name' => 'FAMILY'],
            ['id' => 2, 'name' => 'FRIENDS'],
            ['id' => 3, 'name' => 'SOCIAL'],
            ['id' => 4, 'name' => 'INSTITUTION'],
            ['id' => 5, 'name' => 'BUISINESS'], // <-- keep or correct typo as needed
            ['id' => 6, 'name' => 'CUSTOM'],
            ['id' => 7, 'name' => 'MARINE BOARD'],
            ['id' => 8, 'name' => 'POLICE'],
            ['id' => 9, 'name' => 'LOCAL'],
        ];

        DB::table('guest_categories')->insert($categories);
    }
}
