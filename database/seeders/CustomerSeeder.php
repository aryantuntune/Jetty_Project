<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'mobile' => '9876543210',
                'password' => Hash::make('password'),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'mobile' => '9876543211',
                'password' => Hash::make('password'),
            ],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'mobile' => '9876543212',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
