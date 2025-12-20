<?php

namespace App\Http\Controllers;

use \App\Models\ItemCategory;
use Illuminate\Http\Request;

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
        $query->where('category_name', 'like', '%'.$request->category_name.'%');
    }

    $categories = $query->get();
    $total = $categories->count();

    return view('item_categories.index', compact('categories', 'total'));

    }

    // Show form to create a new category
    public function create()
    {
        return view('item_categories.create');
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
        return view('item_categories.edit', compact('itemCategory'));
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