<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Guest;
use App\Models\GuestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->query('branch_id');

        // Branch dropdown
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::all();
        } elseif ($user->role_id == 3 && $user->route_id) {
            $branches = Branch::whereIn('id', $user->getRouteBranchIds())->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        // Guests query
        $guests = Guest::with('category', 'branch')
            ->when($user->role_id == 3 && $user->route_id, fn($q) => $q->whereIn('branch_id', $user->getRouteBranchIds()))
            ->when($user->role_id == 3 && !$user->route_id, fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($branchId && in_array($user->role_id, [1, 2, 3]), fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $total = $guests->count();

        return Inertia::render('Masters/Guests/Index', [
            'guests' => $guests,
            'total' => $total,
            'branches' => $branches,
            'branchId' => $branchId,
            'user' => $user,
        ]);
    }

    public function create()
    {
        $categories = GuestCategory::all();
        $user = Auth::user();
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::all();
        } elseif ($user->role_id == 3 && $user->route_id) {
            $branches = Branch::whereIn('id', $user->getRouteBranchIds())->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }
        return Inertia::render('Masters/Guests/Create', [
            'categories' => $categories,
            'branches' => $branches,
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id' => 'required_if:user_role,1,2|exists:branches,id',
        ]);

        Guest::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'user_id' => $user->id,
            'branch_id' => in_array($user->role_id, [1, 2, 3]) ? $request->branch_id : $user->branch_id,
        ]);

        return redirect()->route('guests.index')->with('success', 'Guest added successfully!');
    }

    public function edit(Guest $guest)
    {
        $categories = GuestCategory::all();
        $user = Auth::user();
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::all();
        } elseif ($user->role_id == 3 && $user->route_id) {
            $branches = Branch::whereIn('id', $user->getRouteBranchIds())->get();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return Inertia::render('Masters/Guests/Edit', [
            'guest' => $guest,
            'categories' => $categories,
            'branches' => $branches,
            'user' => $user,
        ]);
    }

    public function update(Request $request, Guest $guest)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id' => 'required_if:user_role,1,2|exists:branches,id',
        ]);

        $guest->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'user_id' => $user->id,
            'branch_id' => in_array($user->role_id, [1, 2, 3]) ? $request->branch_id : $user->branch_id,
        ]);

        return redirect()->route('guests.index')->with('success', 'Guest updated successfully!');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('guests.index')->with('success', 'Guest deleted successfully!');
    }


    public function searchById(Request $request)
    {
        $guest = Guest::where('id', $request->id)->first();
        return response()->json($guest);
    }

    public function searchByName(Request $request)
    {
        $guests = Guest::where('name', 'like', '%' . $request->name . '%')->get(['id', 'name']);
        return response()->json($guests);
    }

    public function storebyticket(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $guest = Guest::create($validated);
        return response()->json($guest);
    }

}