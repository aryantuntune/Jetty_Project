<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ITEM CATEGORY REORGANIZATION ===\n\n";

// Step 1: First clear all item_category_id to avoid foreign key issues
echo "Step 1: Clearing item category assignments temporarily...\n";
DB::table('item_rates')->update(['item_category_id' => null]);
echo "  Done! All items now have NULL category.\n\n";

// Step 2: Delete all existing categories
echo "Step 2: Deleting all existing categories...\n";
$deleted = DB::table('item_categories')->delete();
echo "  Deleted {$deleted} old categories.\n\n";

// Step 3: Create new proper categories
echo "Step 3: Creating new categories...\n";
$newCategories = [
    ['id' => 1, 'category_name' => 'PASSENGER', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 2, 'category_name' => 'CYCLE', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 3, 'category_name' => 'MOTORCYCLE', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 4, 'category_name' => 'THREE WHEELER', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 5, 'category_name' => 'TEMPO', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 6, 'category_name' => 'CAR', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 7, 'category_name' => 'BUS', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 8, 'category_name' => 'TRUCK', 'created_at' => now(), 'updated_at' => now()],
    ['id' => 9, 'category_name' => 'AMBULANCE', 'created_at' => now(), 'updated_at' => now()],
];

foreach ($newCategories as $cat) {
    DB::table('item_categories')->insert($cat);
    echo "  Created: {$cat['id']} - {$cat['category_name']}\n";
}
echo "\n";

// Step 4: Reassign items based on item_name patterns
echo "Step 4: Reassigning items to correct categories...\n";

$mappings = [
    // PASSENGER (ID: 1) - Must be first because other patterns might match partial names
    1 => ['PASSENGER ADULT%', 'PASSENGER CHILD%', 'PASSENGER SENIOR%'],

    // CYCLE (ID: 2)
    2 => ['CYCLE'],

    // MOTORCYCLE (ID: 3)
    3 => ['MOTORCYCLE%'],

    // THREE WHEELER (ID: 4)
    4 => ['%3 WHLR%', '%3WHLR%'],

    // TEMPO (ID: 5) - Tata vehicles and 407
    5 => ['TATA ACE%', 'TATA MAGIC%', 'TATA MOBILE%', '407 TEMPO'],

    // CAR (ID: 6)
    6 => ['EMPTY CAR%', 'EMPTY LUX%', 'SUMO%'],

    // BUS (ID: 7)
    7 => ['BUS%', 'MINI BUS%', 'TEMPO TRAVELER%'],

    // TRUCK (ID: 8)
    8 => ['TRUCK%', 'LOADED 709', 'MED.GOODS%'],

    // AMBULANCE (ID: 9)
    9 => ['AMBULANCE'],
];

$totalUpdated = 0;
foreach ($mappings as $categoryId => $patterns) {
    $categoryName = $newCategories[$categoryId - 1]['category_name'];

    foreach ($patterns as $pattern) {
        $updated = DB::table('item_rates')
            ->whereNull('item_category_id')
            ->where('item_name', 'LIKE', $pattern)
            ->update(['item_category_id' => $categoryId]);

        if ($updated > 0) {
            echo "  {$categoryName}: Matched '{$pattern}' → {$updated} items\n";
            $totalUpdated += $updated;
        }
    }
}

echo "\n";

// Step 5: Check for any unassigned items
echo "Step 5: Checking for unassigned items...\n";
$unassigned = DB::table('item_rates')
    ->whereNull('item_category_id')
    ->get();

if ($unassigned->count() > 0) {
    echo "  WARNING: {$unassigned->count()} items still unassigned:\n";
    foreach ($unassigned as $item) {
        echo "    - ID {$item->id}: {$item->item_name}\n";
    }
} else {
    echo "  ✓ All items have been assigned to categories!\n";
}

echo "\n=== SUMMARY ===\n";
echo "Categories created: " . count($newCategories) . "\n";
echo "Items reassigned: {$totalUpdated}\n";
echo "Items unassigned: {$unassigned->count()}\n";

// Final verification
echo "\n=== FINAL CATEGORY COUNTS ===\n";
$counts = DB::table('item_rates')
    ->join('item_categories', 'item_rates.item_category_id', '=', 'item_categories.id')
    ->select('item_categories.category_name', DB::raw('COUNT(*) as count'))
    ->groupBy('item_categories.category_name')
    ->get();

foreach ($counts as $c) {
    echo "  {$c->category_name}: {$c->count} items\n";
}

echo "\n✅ DONE!\n";
