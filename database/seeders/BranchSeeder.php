<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // 12 Jetties from carferry.in - organized by route pairs
        $branches = [
            // Route 1: Dabhol - Dhopave
            ['branch_id' => 101, 'branch_name' => 'DABHOL', 'user_id' => null],
            ['branch_id' => 102, 'branch_name' => 'DHOPAVE', 'user_id' => null],
            // Route 2: Jaigad - Tawsal
            ['branch_id' => 103, 'branch_name' => 'JAIGAD', 'user_id' => null],
            ['branch_id' => 104, 'branch_name' => 'TAWSAL', 'user_id' => null],
            // Route 3: Dighi - Agardanda
            ['branch_id' => 105, 'branch_name' => 'DIGHI', 'user_id' => null],
            ['branch_id' => 106, 'branch_name' => 'AGARDANDA', 'user_id' => null],
            // Route 4: Veshvi - Bagmandale
            ['branch_id' => 107, 'branch_name' => 'VESHVI', 'user_id' => null],
            ['branch_id' => 108, 'branch_name' => 'BAGMANDALE', 'user_id' => null],
            // Route 5: Vasai - Bhayander
            ['branch_id' => 109, 'branch_name' => 'VASAI', 'user_id' => null],
            ['branch_id' => 110, 'branch_name' => 'BHAYANDER', 'user_id' => null],
            // Route 6: Virar - Saphale
            ['branch_id' => 111, 'branch_name' => 'VIRAR', 'user_id' => null],
            ['branch_id' => 112, 'branch_name' => 'SAPHALE', 'user_id' => null],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
