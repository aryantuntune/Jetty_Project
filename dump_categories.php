<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== ITEM CATEGORIES ===\n";
foreach (\App\Models\ItemCategory::orderBy('id')->get() as $cat) {
    echo "{$cat->id} - {$cat->category_name}\n";
}

echo "\n=== ITEM RATES (grouped by category) ===\n";
$items = \App\Models\ItemRate::with('category')
    ->orderBy('item_category_id')
    ->orderBy('item_name')
    ->get();

$currentCat = null;
foreach ($items as $item) {
    $catName = $item->category->category_name ?? 'UNCATEGORIZED';
    if ($currentCat !== $item->item_category_id) {
        echo "\n--- {$catName} (ID: {$item->item_category_id}) ---\n";
        $currentCat = $item->item_category_id;
    }
    echo "  {$item->id}: {$item->item_name}\n";
}
