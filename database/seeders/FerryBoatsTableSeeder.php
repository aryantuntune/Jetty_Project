<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerryBoat;

class FerryBoatsTableSeeder extends Seeder
{
    public function run()
    {
        $boats = [
            ['number' => 'RTNIV00001', 'name' => 'SHANTADURGA', 'branch_id' => 1, 'user_id' => 1],
            ['number' => 'RTNIV00007', 'name' => 'SONIA', 'branch_id' => 1, 'user_id' => 1],
            ['number' => 'RTNIV00010', 'name' => 'PRIYANKA', 'branch_id' => 2, 'user_id' => 1],
            ['number' => 'RTNIV00011', 'name' => 'SUPRIYA', 'branch_id' => 2, 'user_id' => 1],
            ['number' => 'RTNIV00030', 'name' => 'AISHWARYA', 'branch_id' => 3, 'user_id' => 1],
            ['number' => 'RTNIV030082', 'name' => 'AVANTIKA', 'branch_id' => 3, 'user_id' => 1],
            ['number' => 'RTN-IV-124', 'name' => 'VAIBHAVI', 'branch_id' => 4, 'user_id' => 1],
            ['number' => 'RTN-IV-125', 'name' => 'AAROHI', 'branch_id' => 4, 'user_id' => 1],
            ['number' => 'RTN-IV-137', 'name' => 'JANHAVI', 'branch_id' => 5, 'user_id' => 1],
            ['number' => 'RTN-IV-159', 'name' => 'DEVIKA', 'branch_id' => 5, 'user_id' => 1],
        ];

        foreach ($boats as $boat) {
            FerryBoat::create($boat);
        }
    }
}
