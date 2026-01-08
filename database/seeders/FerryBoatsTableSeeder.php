<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerryBoat;

class FerryBoatsTableSeeder extends Seeder
{
    /**
     * Ferryboats for each route.
     * Note: Only AAROHI is confirmed from carferry.in (Virar-Saphale route).
     * Other names are placeholders - update when actual names are known.
     */
    public function run()
    {
        $boats = [
            // Route 1: Dabhol - Dhopave (both jetties share same ferry)
            ['number' => 'RTNIV-001', 'name' => 'FERRY 1', 'user_id' => null, 'branch_id' => 1],
            ['number' => 'RTNIV-001', 'name' => 'FERRY 1', 'user_id' => null, 'branch_id' => 2],

            // Route 2: Jaigad - Tawsal
            ['number' => 'RTNIV-002', 'name' => 'FERRY 2', 'user_id' => null, 'branch_id' => 3],
            ['number' => 'RTNIV-002', 'name' => 'FERRY 2', 'user_id' => null, 'branch_id' => 4],

            // Route 3: Dighi - Agardanda
            ['number' => 'RTNIV-003', 'name' => 'FERRY 3', 'user_id' => null, 'branch_id' => 5],
            ['number' => 'RTNIV-003', 'name' => 'FERRY 3', 'user_id' => null, 'branch_id' => 6],

            // Route 4: Veshvi - Bagmandale
            ['number' => 'RTNIV-004', 'name' => 'FERRY 4', 'user_id' => null, 'branch_id' => 7],
            ['number' => 'RTNIV-004', 'name' => 'FERRY 4', 'user_id' => null, 'branch_id' => 8],

            // Route 5: Vasai - Bhayander
            ['number' => 'RTNIV-005', 'name' => 'FERRY 5', 'user_id' => null, 'branch_id' => 9],
            ['number' => 'RTNIV-005', 'name' => 'FERRY 5', 'user_id' => null, 'branch_id' => 10],

            // Route 6: Virar - Saphale (AAROHI - confirmed from carferry.in)
            ['number' => 'RTNIV-006', 'name' => 'AAROHI', 'user_id' => null, 'branch_id' => 11],
            ['number' => 'RTNIV-006', 'name' => 'AAROHI', 'user_id' => null, 'branch_id' => 12],
        ];

        foreach ($boats as $boat) {
            FerryBoat::updateOrCreate(
                ['number' => $boat['number'], 'branch_id' => $boat['branch_id']],
                $boat
            );
        }
    }
}
