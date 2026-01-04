<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 6 Routes from carferry.in (each route is a pair of jetties):
     * Route 1: Dabhol - Dhopave (started 21.10.2003)
     * Route 2: Jaigad - Tawsal
     * Route 3: Dighi - Agardanda
     * Route 4: Veshvi - Bagmandale (started 2007)
     * Route 5: Vasai - Bhayander
     * Route 6: Virar - Saphale (RORO service)
     */
    public function run(): void
    {
        $routes = [
            // Route 1: Dabhol (branch_id=1) <-> Dhopave (branch_id=2)
            ['route_id' => 1, 'branch_id' => 1],
            ['route_id' => 1, 'branch_id' => 2],

            // Route 2: Jaigad (branch_id=3) <-> Tawsal (branch_id=4)
            ['route_id' => 2, 'branch_id' => 3],
            ['route_id' => 2, 'branch_id' => 4],

            // Route 3: Dighi (branch_id=5) <-> Agardanda (branch_id=6)
            ['route_id' => 3, 'branch_id' => 5],
            ['route_id' => 3, 'branch_id' => 6],

            // Route 4: Veshvi (branch_id=7) <-> Bagmandale (branch_id=8)
            ['route_id' => 4, 'branch_id' => 7],
            ['route_id' => 4, 'branch_id' => 8],

            // Route 5: Vasai (branch_id=9) <-> Bhayander (branch_id=10)
            ['route_id' => 5, 'branch_id' => 9],
            ['route_id' => 5, 'branch_id' => 10],

            // Route 6: Virar (branch_id=11) <-> Saphale (branch_id=12)
            ['route_id' => 6, 'branch_id' => 11],
            ['route_id' => 6, 'branch_id' => 12],
        ];

        foreach ($routes as $route) {
            DB::table('routes')->updateOrInsert(
                [
                    'route_id' => $route['route_id'],
                    'branch_id' => $route['branch_id']
                ],
                []
            );
        }
    }
}
