<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['branch_id' => 101, 'branch_name' => 'DABHOL'],
            ['branch_id' => 102, 'branch_name' => 'DHOPAVE'],
            ['branch_id' => 103, 'branch_name' => 'VESHVI'],
            ['branch_id' => 104, 'branch_name' => 'BAGMANDALE'],
            ['branch_id' => 105, 'branch_name' => 'JAIGAD'],
            ['branch_id' => 106, 'branch_name' => 'TAVSAL'],
            ['branch_id' => 107, 'branch_name' => 'AGARDANDA'],
            ['branch_id' => 108, 'branch_name' => 'DIGHI'],
            ['branch_id' => 109, 'branch_name' => 'VASAI'],
            ['branch_id' => 110, 'branch_name' => 'BHAYANDER'],
            ['branch_id' => 111, 'branch_name' => 'VIRAR [MARAMBALPADA]'],
            ['branch_id' => 112, 'branch_name' => 'SAPHALE [KHARWADASHRI]'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
