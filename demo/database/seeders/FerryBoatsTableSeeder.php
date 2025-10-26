<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerryBoat;

class FerryBoatsTableSeeder extends Seeder
{
    public function run()
    {
        $boats = [
            ['number' => 'RTNIV00001', 'name' => 'SHANTADURGA'],
            ['number' => 'RTNIV00007', 'name' => 'SONIA'],
            ['number' => 'RTNIV00010', 'name' => 'PRIYANKA'],
            ['number' => 'RTNIV00011', 'name' => 'SUPRIYA'],
            ['number' => 'RTNIV00030', 'name' => 'AISHWARYA'],
            ['number' => 'RTNIV030082', 'name' => 'AVANTIKA'],
            ['number' => 'RTN-IV-124', 'name' => 'VAIBHAVI'],
            ['number' => 'RTN-IV-125', 'name' => 'AAROHI'],
            ['number' => 'RTN-IV-137', 'name' => 'JANHAVI'],
            ['number' => 'RTN-IV-159', 'name' => 'DEVIKA'],
        ];

        foreach ($boats as $boat) {
            FerryBoat::create($boat);
        }
    }
}
