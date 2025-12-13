<?php

namespace Database\Seeders;

use App\Models\ItemRate;
use Illuminate\Database\Seeder;

class ItemRatesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['CYCLE',                               1,  13.00,  2.00],
            ['MOTORCYCLE WITH DRIVER',              1,  58.00,  7.00],
            ['EMPTY 3 WHLR RICKSHAW',               1,  81.00,  9.00],
            ['EMPTY 3WHLR 5 ST RICKSHAW',           1,   0.00,  0.00],
            ['TATA MAGIC/MAXIMO 6 ST',              1, 153.00, 17.00],
            ['TATA ACE/MAXIMO TEMPO',               1, 153.00, 17.00],
            ['EMPTY CAR 5 ST',                      1, 163.00, 17.00],
            ['EMPTY LUX. CAR 5 ST',                 1, 181.00, 19.00],
            ['SUMO/SCAPIO/TAVERA/INOVA 7 ST',       1, 181.00, 19.00],
            ['TATA MOBILE/MAX PICKUP',              1, 181.00, 19.00],
            ['PASSENGER ADULT ABOVE 12 YR',         1,  18.00,  2.00],
            ['PASSENGER CHILD 3-12 YR',             1,   9.00,  0.00],
            ['AMBULANCE',                            1, 180.00,  0.00],
            ['TEMPO TRAVELER/18 ST BUS',            1, 215.00, 25.00],
            ['407 TEMPO',                            1, 215.00, 25.00],
            ['MINI BUS 21 ST',                       1, 225.00, 25.00],
            ['LOADED 709',                           1,  70.00,  0.00],
            ['MED.GOODS 6 WHLR  (709)',              1, 225.00, 25.00],
            ['TRUCK /TANKER',                        1, 360.00, 40.00],
            ['PASSENGER BUS',                        1, 360.00, 40.00],
        ];

        foreach ($rows as [$name, $cat, $rate, $levy]) {
            ItemRate::updateOrCreate(
                [
                    'item_name'        => $name,
                    'item_category_id' => $cat,
                    'branch_id'        => 1, // References branches.id (DABHOL)
                    'starting_date'    => '2024-10-11',
                ],
                [
                    'item_rate'  => $rate,
                    'item_lavy'  => $levy,
                    'ending_date'=> null,
                    'user_id'    => 1, // set to an existing user id or null
                ]
            );
        }
    }
}