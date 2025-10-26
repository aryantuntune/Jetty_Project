<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestCategory;

class GuestCategoryController extends Controller
{
    public function index()
    {
        $categories = GuestCategory::all();
        return view('guest_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('guest_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:guest_categories,name'
        ]);

        GuestCategory::create(['name' => $request->name]);
        return redirect()->route('guest_categories.index')->with('success', 'Category added successfully!');
    }

    public function edit(GuestCategory $guestCategory)
    {
        return view('guest_categories.edit', compact('guestCategory'));
    }

    public function update(Request $request, GuestCategory $guestCategory)
    {
        $request->validate([
            'name' => 'required|unique:guest_categories,name,' . $guestCategory->id,
        ]);

        $guestCategory->update(['name' => $request->name]);
        return redirect()->route('guest_categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(GuestCategory $guestCategory)
    {
        $guestCategory->delete();
        return redirect()->route('guest_categories.index')->with('success', 'Category deleted successfully!');
    }
}

