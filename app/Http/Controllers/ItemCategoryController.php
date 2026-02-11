<?php

namespace App\Http\Controllers;

use \App\Models\ItemCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemCategory::query();

        // Filter by ID
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by Category Name
        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%' . $request->category_name . '%');
        }

        $categories = $query->with([
            'items' => function ($q) {
                $q->select('id', 'item_name', 'item_rate', 'item_lavy', 'item_category_id', 'is_active')
                    ->orderBy('item_name');
            }
        ])->get();
        $total = $categories->count();

        return Inertia::render('Masters/ItemCategories/Index', [
            'categories' => $categories,
            'total' => $total,
        ]);

    }

    // Show form to create a new category
    public function create()
    {
        return Inertia::render('Masters/ItemCategories/Create');
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            // 'levy' => 'required|numeric|min:0',
        ]);

        ItemCategory::create([
            'category_name' => $request->category_name,
            // 'levy' => $request->levy,
        ]);

        return redirect()->route('item_categories.index')
            ->with('success', 'Item Category added successfully.');
    }

    // Show form to edit existing category
    public function edit(ItemCategory $itemCategory)
    {
        return Inertia::render('Masters/ItemCategories/Edit', ['itemCategory' => $itemCategory]);
    }

    // Update existing category
    public function update(Request $request, ItemCategory $itemCategory)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'levy' => 'required|numeric|min:0',
        ]);

        $itemCategory->update([
            'category_name' => $request->category_name,
            'levy' => $request->levy,
        ]);

        return redirect()->route('item_categories.index')
            ->with('success', 'Item Category updated successfully.');
    }

    // Delete a category
    public function destroy(ItemCategory $itemCategory)
    {
        $itemCategory->delete();

        return redirect()->route('item_categories.index')
            ->with('success', 'Item Category deleted successfully.');
    }

}