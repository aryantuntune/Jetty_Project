<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemCategory;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'CYCLE', 'levy' => 0.00],
            ['category_name' => 'PASSENGER MONTHLY PASS', 'levy' => 15.00],
            ['category_name' => 'MOTORCYCLE WITH DRIVER', 'levy' => 2.00],
            ['category_name' => 'EMPTY 3 WHEELER RICKSHAW', 'levy' => 3.00],
            ['category_name' => 'EMPTY 3 WHEELER RICKSHAW TEMPO', 'levy' => 7.00],
            ['category_name' => 'EMPTY CAR 5 SIT', 'levy' => 7.00],
            ['category_name' => '5 SEATER CAR & MAGIC, MAXMO', 'levy' => 7.00],
            ['category_name' => 'EMPTY LUX.CAR & 8 SITER, 407 TRACTO', 'levy' => 7.00],
            ['category_name' => 'AMBULANCE', 'levy' => 0.00],
            ['category_name' => 'EMPTY SIX WHEELERS VEHICLES EX.709', 'levy' => 20.00],
            ['category_name' => 'EMPTY BUS,TRUCK OR TANKER', 'levy' => 30.00],
            ['category_name' => 'EMPTY 10 WHEELER GOODS TRUCK OR JCB', 'levy' => 30.00],
            ['category_name' => 'GOODS PER HALF TON', 'levy' => 5.00],
            ['category_name' => 'PASSENGERS ADULTS (ABOVE 12 YEARS)', 'levy' => 0.50],
            ['category_name' => 'PASSENGERS CHILDREN (3 TO 12 YEARS)', 'levy' => 0.25],
            ['category_name' => 'PASSENGER LUGGAGE (ABOVE 20 KG)', 'levy' => 1.00],
            ['category_name' => 'DOG GOATS SHEEP (PER NOS.)', 'levy' => 1.00],
            ['category_name' => 'COWS BULLS BUFFALOWS (PER NOS.)', 'levy' => 2.00],
            ['category_name' => 'PARTY', 'levy' => 0.00],
            ['category_name' => 'IMARGANCY FERRY', 'levy' => 0.00],
        ];

        foreach ($categories as $category) {
            ItemCategory::updateOrCreate(
                ['category_name' => $category['category_name']],
                $category
            );
        }
    }
}
