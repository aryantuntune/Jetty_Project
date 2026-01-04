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
            ['branch_id' => 101, 'branch_name' => 'DABHOL'],
            ['branch_id' => 102, 'branch_name' => 'DHOPAVE'],
            // Route 2: Jaigad - Tawsal
            ['branch_id' => 103, 'branch_name' => 'JAIGAD'],
            ['branch_id' => 104, 'branch_name' => 'TAWSAL'],
            // Route 3: Dighi - Agardanda
            ['branch_id' => 105, 'branch_name' => 'DIGHI'],
            ['branch_id' => 106, 'branch_name' => 'AGARDANDA'],
            // Route 4: Veshvi - Bagmandale
            ['branch_id' => 107, 'branch_name' => 'VESHVI'],
            ['branch_id' => 108, 'branch_name' => 'BAGMANDALE'],
            // Route 5: Vasai - Bhayander
            ['branch_id' => 109, 'branch_name' => 'VASAI'],
            ['branch_id' => 110, 'branch_name' => 'BHAYANDER'],
            // Route 6: Virar - Saphale
            ['branch_id' => 111, 'branch_name' => 'VIRAR'],
            ['branch_id' => 112, 'branch_name' => 'SAPHALE'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
