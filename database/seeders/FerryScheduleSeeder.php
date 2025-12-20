<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FerrySchedule;

class FerryScheduleSeeder extends Seeder
{
    public function run()
    {
        $schedules = [
            ['hour' => 6, 'minute' => 30],
            ['hour' => 7, 'minute' => 15],
            ['hour' => 8, 'minute' => 15],
            ['hour' => 9, 'minute' => 0],
            ['hour' => 9, 'minute' => 45],
            ['hour' => 10, 'minute' => 30],
            ['hour' => 11, 'minute' => 15],
            ['hour' => 12, 'minute' => 0],
            ['hour' => 12, 'minute' => 40],
            ['hour' => 13, 'minute' => 35],
            ['hour' => 14, 'minute' => 15],
            ['hour' => 15, 'minute' => 0],
            ['hour' => 15, 'minute' => 30],
            ['hour' => 15, 'minute' => 45],
            ['hour' => 16, 'minute' => 30],
            ['hour' => 17, 'minute' => 15],
            ['hour' => 18, 'minute' => 0],
            ['hour' => 18, 'minute' => 45],
            ['hour' => 19, 'minute' => 30],
            ['hour' => 20, 'minute' => 15],
            ['hour' => 21, 'minute' => 0],
            ['hour' => 22, 'minute' => 0],
            ['hour' => 23, 'minute' => 0],
            ['hour' => 23, 'minute' => 30],
        ];

        foreach ($schedules as $schedule) {
            FerrySchedule::create([
                'hour' => $schedule['hour'],
                'minute' => $schedule['minute'],
            ]);
        }
    }
}
