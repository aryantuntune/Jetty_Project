<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            // From DABHOL (101) -> branch id 1
            ['from_branch_id' => 1, 'to_branch_id' => 2, 'distance_km' => 15, 'duration_minutes' => 30, 'base_fare' => 50.00, 'status' => 'active'],
            ['from_branch_id' => 1, 'to_branch_id' => 3, 'distance_km' => 25, 'duration_minutes' => 45, 'base_fare' => 75.00, 'status' => 'active'],
            ['from_branch_id' => 1, 'to_branch_id' => 4, 'distance_km' => 35, 'duration_minutes' => 60, 'base_fare' => 100.00, 'status' => 'active'],

            // From DHOPAVE (102) -> branch id 2
            ['from_branch_id' => 2, 'to_branch_id' => 1, 'distance_km' => 15, 'duration_minutes' => 30, 'base_fare' => 50.00, 'status' => 'active'],
            ['from_branch_id' => 2, 'to_branch_id' => 3, 'distance_km' => 10, 'duration_minutes' => 20, 'base_fare' => 40.00, 'status' => 'active'],
            ['from_branch_id' => 2, 'to_branch_id' => 4, 'distance_km' => 20, 'duration_minutes' => 35, 'base_fare' => 60.00, 'status' => 'active'],

            // From VESHVI (103) -> branch id 3
            ['from_branch_id' => 3, 'to_branch_id' => 1, 'distance_km' => 25, 'duration_minutes' => 45, 'base_fare' => 75.00, 'status' => 'active'],
            ['from_branch_id' => 3, 'to_branch_id' => 2, 'distance_km' => 10, 'duration_minutes' => 20, 'base_fare' => 40.00, 'status' => 'active'],
            ['from_branch_id' => 3, 'to_branch_id' => 4, 'distance_km' => 12, 'duration_minutes' => 25, 'base_fare' => 45.00, 'status' => 'active'],

            // From BAGMANDALE (104) -> branch id 4
            ['from_branch_id' => 4, 'to_branch_id' => 1, 'distance_km' => 35, 'duration_minutes' => 60, 'base_fare' => 100.00, 'status' => 'active'],
            ['from_branch_id' => 4, 'to_branch_id' => 2, 'distance_km' => 20, 'duration_minutes' => 35, 'base_fare' => 60.00, 'status' => 'active'],
            ['from_branch_id' => 4, 'to_branch_id' => 3, 'distance_km' => 12, 'duration_minutes' => 25, 'base_fare' => 45.00, 'status' => 'active'],

            // From JAIGAD (105) -> branch id 5
            ['from_branch_id' => 5, 'to_branch_id' => 1, 'distance_km' => 40, 'duration_minutes' => 70, 'base_fare' => 120.00, 'status' => 'active'],
            ['from_branch_id' => 5, 'to_branch_id' => 6, 'distance_km' => 18, 'duration_minutes' => 35, 'base_fare' => 55.00, 'status' => 'active'],

            // From TAVSAL (106) -> branch id 6
            ['from_branch_id' => 6, 'to_branch_id' => 5, 'distance_km' => 18, 'duration_minutes' => 35, 'base_fare' => 55.00, 'status' => 'active'],
            ['from_branch_id' => 6, 'to_branch_id' => 7, 'distance_km' => 22, 'duration_minutes' => 40, 'base_fare' => 65.00, 'status' => 'active'],

            // From AGARDANDA (107) -> branch id 7
            ['from_branch_id' => 7, 'to_branch_id' => 6, 'distance_km' => 22, 'duration_minutes' => 40, 'base_fare' => 65.00, 'status' => 'active'],
            ['from_branch_id' => 7, 'to_branch_id' => 8, 'distance_km' => 28, 'duration_minutes' => 50, 'base_fare' => 85.00, 'status' => 'active'],

            // From DIGHI (108) -> branch id 8
            ['from_branch_id' => 8, 'to_branch_id' => 7, 'distance_km' => 28, 'duration_minutes' => 50, 'base_fare' => 85.00, 'status' => 'active'],
            ['from_branch_id' => 8, 'to_branch_id' => 9, 'distance_km' => 30, 'duration_minutes' => 55, 'base_fare' => 90.00, 'status' => 'active'],

            // From VASAI (109) -> branch id 9
            ['from_branch_id' => 9, 'to_branch_id' => 8, 'distance_km' => 30, 'duration_minutes' => 55, 'base_fare' => 90.00, 'status' => 'active'],
            ['from_branch_id' => 9, 'to_branch_id' => 10, 'distance_km' => 8, 'duration_minutes' => 15, 'base_fare' => 30.00, 'status' => 'active'],

            // From BHAYANDER (110) -> branch id 10
            ['from_branch_id' => 10, 'to_branch_id' => 9, 'distance_km' => 8, 'duration_minutes' => 15, 'base_fare' => 30.00, 'status' => 'active'],
            ['from_branch_id' => 10, 'to_branch_id' => 11, 'distance_km' => 12, 'duration_minutes' => 20, 'base_fare' => 40.00, 'status' => 'active'],

            // From VIRAR (111) -> branch id 11
            ['from_branch_id' => 11, 'to_branch_id' => 10, 'distance_km' => 12, 'duration_minutes' => 20, 'base_fare' => 40.00, 'status' => 'active'],
            ['from_branch_id' => 11, 'to_branch_id' => 12, 'distance_km' => 14, 'duration_minutes' => 25, 'base_fare' => 45.00, 'status' => 'active'],

            // From SAPHALE (112) -> branch id 12
            ['from_branch_id' => 12, 'to_branch_id' => 11, 'distance_km' => 14, 'duration_minutes' => 25, 'base_fare' => 45.00, 'status' => 'active'],
            ['from_branch_id' => 12, 'to_branch_id' => 1, 'distance_km' => 50, 'duration_minutes' => 90, 'base_fare' => 150.00, 'status' => 'active'],
        ];

        foreach ($routes as $route) {
            DB::table('routes')->insert(array_merge($route, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
