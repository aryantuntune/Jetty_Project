<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['branch_id' => 101, 'branch_name' => 'DABHOL', 'user_id' => 1],
            ['branch_id' => 102, 'branch_name' => 'DHOPAVE', 'user_id' => 1],
            ['branch_id' => 103, 'branch_name' => 'VESHVI', 'user_id' => 1],
            ['branch_id' => 104, 'branch_name' => 'BAGMANDALE', 'user_id' => 1],
            ['branch_id' => 105, 'branch_name' => 'JAIGAD', 'user_id' => 1],
            ['branch_id' => 106, 'branch_name' => 'TAVSAL', 'user_id' => 1],
            ['branch_id' => 107, 'branch_name' => 'AGARDANDA', 'user_id' => 1],
            ['branch_id' => 108, 'branch_name' => 'DIGHI', 'user_id' => 1],
            ['branch_id' => 109, 'branch_name' => 'VASAI', 'user_id' => 1],
            ['branch_id' => 110, 'branch_name' => 'BHAYANDER', 'user_id' => 1],
            ['branch_id' => 111, 'branch_name' => 'VIRAR [MARAMBALPADA]', 'user_id' => 1],
            ['branch_id' => 112, 'branch_name' => 'SAPHALE [KHARWADASHRI]', 'user_id' => 1],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
