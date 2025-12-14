<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerryBoat;

class FerryBoatsTableSeeder extends Seeder
{
    public function run()
    {
        $boats = [
            ['number' => 'RTNIV00001', 'name' => 'SHANTADURGA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTNIV00007', 'name' => 'SONIA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTNIV00010', 'name' => 'PRIYANKA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTNIV00011', 'name' => 'SUPRIYA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTNIV00030', 'name' => 'AISHWARYA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTNIV030082', 'name' => 'AVANTIKA', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTN-IV-124', 'name' => 'VAIBHAVI', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTN-IV-125', 'name' => 'AAROHI', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTN-IV-137', 'name' => 'JANHAVI', 'user_id' => 1, 'branch_id' => 1],
            ['number' => 'RTN-IV-159', 'name' => 'DEVIKA', 'user_id' => 1, 'branch_id' => 1],
        ];

        foreach ($boats as $boat) {
            FerryBoat::create($boat);
        }
    }
}
