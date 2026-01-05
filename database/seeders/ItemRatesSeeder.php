<?php

namespace Database\Seeders;

use App\Models\ItemRate;
use Illuminate\Database\Seeder;

class ItemRatesSeeder extends Seeder
{
    public function run(): void
    {
        // [name, category_id, rate, levy, is_vehicle]
        $rows = [
            // Passenger types (MUST be first for app logic)
            ['PASSENGER ADULT ABOVE 12 YR',         1,  18.00,  2.00, false],
            ['PASSENGER CHILD 3-12 YR',             1,   9.00,  0.00, false],
            ['PASSENGER SENIOR CITIZEN',            1,  15.00,  2.00, false],

            // Vehicles
            ['CYCLE',                               2,  13.00,  2.00, true],
            ['MOTORCYCLE WITH DRIVER',              2,  58.00,  7.00, true],
            ['EMPTY 3 WHLR RICKSHAW',               2,  81.00,  9.00, true],
            ['EMPTY 3WHLR 5 ST RICKSHAW',           2,   0.00,  0.00, true],
            ['TATA MAGIC/MAXIMO 6 ST',              2, 153.00, 17.00, true],
            ['TATA ACE/MAXIMO TEMPO',               2, 153.00, 17.00, true],
            ['EMPTY CAR 5 ST',                      2, 163.00, 17.00, true],
            ['EMPTY LUX. CAR 5 ST',                 2, 181.00, 19.00, true],
            ['SUMO/SCAPIO/TAVERA/INOVA 7 ST',       2, 181.00, 19.00, true],
            ['TATA MOBILE/MAX PICKUP',              2, 181.00, 19.00, true],
            ['AMBULANCE',                           2, 180.00,  0.00, true],
            ['TEMPO TRAVELER/18 ST BUS',            2, 215.00, 25.00, true],
            ['407 TEMPO',                           2, 215.00, 25.00, true],
            ['MINI BUS 21 ST',                      2, 225.00, 25.00, true],
            ['BUS (PASSENGER)',                     2, 360.00, 40.00, true],
            ['LOADED 709',                          2,  70.00,  0.00, true],
            ['MED.GOODS 6 WHLR  (709)',             2, 225.00, 25.00, true],
            ['TRUCK /TANKER',                       2, 360.00, 40.00, true],
        ];

        // Apply rates to all branches (1-12)
        for ($branchId = 1; $branchId <= 12; $branchId++) {
            foreach ($rows as $index => [$name, $cat, $rate, $levy, $isVehicle]) {
                ItemRate::create([
                    'item_name'        => $name,
                    'item_category_id' => $cat,
                    'item_rate'        => $rate,
                    'item_lavy'        => $levy,
                    'is_vehicle'       => $isVehicle,
                    'branch_id'        => $branchId,
                    'starting_date'    => '2024-10-11',
                    'ending_date'      => null,
                    'is_active'        => true,
                ]);
            }
        }
    }
}
