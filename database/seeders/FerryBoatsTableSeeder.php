<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerryBoat;

class FerryBoatsTableSeeder extends Seeder
{
    public function run()
    {
        $boats = [
            // Branch 1: DABHOL - Keep existing
            ['number' => 'RTNIV00001', 'name' => 'SHANTADURGA', 'user_id' => 1, 'branch_id' => 1],

            // Branch 2: DHOPAVE
            ['number' => 'RTNIV00007', 'name' => 'SONIA', 'user_id' => 1, 'branch_id' => 2],

            // Branch 3: VESHVI
            ['number' => 'RTNIV00010', 'name' => 'PRIYANKA', 'user_id' => 1, 'branch_id' => 3],

            // Branch 4: BAGMANDALE
            ['number' => 'RTNIV00011', 'name' => 'SUPRIYA', 'user_id' => 1, 'branch_id' => 4],

            // Branch 5: JAIGAD
            ['number' => 'RTNIV00030', 'name' => 'AISHWARYA', 'user_id' => 1, 'branch_id' => 5],

            // Branch 6: TAVSAL
            ['number' => 'RTNIV030082', 'name' => 'AVANTIKA', 'user_id' => 1, 'branch_id' => 6],

            // Branch 7: AGARDANDA
            ['number' => 'RTN-IV-124', 'name' => 'VAIBHAVI', 'user_id' => 1, 'branch_id' => 7],

            // Branch 8: DIGHI
            ['number' => 'RTN-IV-125', 'name' => 'AAROHI', 'user_id' => 1, 'branch_id' => 8],

            // Branch 9: VASAI
            ['number' => 'RTN-IV-137', 'name' => 'JANHAVI', 'user_id' => 1, 'branch_id' => 9],

            // Branch 10: BHAYANDER
            ['number' => 'RTN-IV-159', 'name' => 'DEVIKA', 'user_id' => 1, 'branch_id' => 10],
        ];

        foreach ($boats as $boat) {
            FerryBoat::updateOrCreate(
                ['number' => $boat['number']], // Match by ferry number
                $boat // Update or create with these values
            );
        }
    }
}
