<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutesSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            // Route 1: DABHOL - DHOPAVE - VESHVI - BAGMANDALE (Southern route)
            ['route_id' => 1, 'branch_id' => 1], // DABHOL
            ['route_id' => 1, 'branch_id' => 2], // DHOPAVE
            ['route_id' => 1, 'branch_id' => 3], // VESHVI
            ['route_id' => 1, 'branch_id' => 4], // BAGMANDALE

            // Route 2: JAIGAD - TAVSAL - AGARDANDA (Middle route)
            ['route_id' => 2, 'branch_id' => 5], // JAIGAD
            ['route_id' => 2, 'branch_id' => 6], // TAVSAL
            ['route_id' => 2, 'branch_id' => 7], // AGARDANDA

            // Route 3: DIGHI - VASAI - BHAYANDER - VIRAR - SAPHALE (Northern route)
            ['route_id' => 3, 'branch_id' => 8],  // DIGHI
            ['route_id' => 3, 'branch_id' => 9],  // VASAI
            ['route_id' => 3, 'branch_id' => 10], // BHAYANDER
            ['route_id' => 3, 'branch_id' => 11], // VIRAR
            ['route_id' => 3, 'branch_id' => 12], // SAPHALE
        ];

        foreach ($routes as $route) {
            DB::table('routes')->insert(array_merge($route, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
