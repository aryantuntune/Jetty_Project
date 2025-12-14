<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Route 1: DABHOL <-> DHOPAVE <-> VESHVI
        // This is a common coastal route
        $route1 = [
            ['route_id' => 1, 'branch_id' => 1, 'sequence' => 1], // DABHOL (id=1 from branches)
            ['route_id' => 1, 'branch_id' => 2, 'sequence' => 2], // DHOPAVE
            ['route_id' => 1, 'branch_id' => 3, 'sequence' => 3], // VESHVI
        ];

        // Route 2: DABHOL <-> BAGMANDALE <-> JAIGAD
        $route2 = [
            ['route_id' => 2, 'branch_id' => 1, 'sequence' => 1], // DABHOL
            ['route_id' => 2, 'branch_id' => 4, 'sequence' => 2], // BAGMANDALE
            ['route_id' => 2, 'branch_id' => 5, 'sequence' => 3], // JAIGAD
        ];

        // Route 3: TAVSAL <-> AGARDANDA <-> DIGHI
        $route3 = [
            ['route_id' => 3, 'branch_id' => 6, 'sequence' => 1], // TAVSAL
            ['route_id' => 3, 'branch_id' => 7, 'sequence' => 2], // AGARDANDA
            ['route_id' => 3, 'branch_id' => 8, 'sequence' => 3], // DIGHI
        ];

        // Route 4: VASAI <-> BHAYANDER <-> VIRAR
        // This is for the Mumbai area ferries
        $route4 = [
            ['route_id' => 4, 'branch_id' => 9, 'sequence' => 1],  // VASAI
            ['route_id' => 4, 'branch_id' => 10, 'sequence' => 2], // BHAYANDER
            ['route_id' => 4, 'branch_id' => 11, 'sequence' => 3], // VIRAR
        ];

        // Combine all routes
        $allRoutes = array_merge($route1, $route2, $route3, $route4);

        // Insert all routes (without timestamps since the table doesn't have them)
        foreach ($allRoutes as $route) {
            DB::table('routes')->updateOrInsert(
                [
                    'route_id' => $route['route_id'],
                    'branch_id' => $route['branch_id']
                ],
                [
                    'sequence' => $route['sequence']
                ]
            );
        }
    }
}
