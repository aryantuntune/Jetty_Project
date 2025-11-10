<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Guest;
use App\Models\GuestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->query('branch_id');

        // Branch dropdown
        if (in_array($user->role_id, [1,2])) {
            $branches = Branch::all(); // all branches
        } else {
            $branches = Branch::where('id', $user->branch_id)->get(); // only user's branch
        }

        // Guests query
        $guests = Guest::with('category','branch')
            ->when($user->role_id == 3, fn($q) => $q->where('branch_id', $user->branch_id)) // role 3 sees only own branch
            ->when($branchId && in_array($user->role_id, [1,2]), fn($q) => $q->where('branch_id', $branchId)) // filter by dropdown for 1,2
            ->get();

        $total = $guests->count();

        return view('guests.index', compact('guests', 'total', 'branches', 'branchId', 'user'));
    }

    public function create()
    {
        $categories = GuestCategory::all();
        $user = Auth::user();
        $branches = in_array($user->role_id, [1,2]) ? Branch::all() : Branch::where('id', $user->branch_id)->get();
        return view('guests.create', compact('categories', 'branches', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id'   => 'required_if:user_role,1,2|exists:branches,id',
        ]);

        Guest::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'user_id'     => $user->id,
            'branch_id'   => in_array($user->role_id, [1,2]) ? $request->branch_id : $user->branch_id,
        ]);

        return redirect()->route('guests.index')->with('success', 'Guest added successfully!');
    }

    public function edit(Guest $guest)
    {
        $categories = GuestCategory::all();
        $user = Auth::user();
        $branches = in_array($user->role_id, [1,2]) ? Branch::all() : Branch::where('id', $user->branch_id)->get();

        return view('guests.edit', compact('guest', 'categories', 'branches', 'user'));
    }

    public function update(Request $request, Guest $guest)
    {
        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id'   => 'required_if:user_role,1,2|exists:branches,id',
        ]);

        $guest->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'user_id'     => $user->id,
            'branch_id'   => in_array($user->role_id, [1,2]) ? $request->branch_id : $user->branch_id,
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
    $guest = Guest::find($request->id);

    if (!$guest) {
        return response()->json(['ok' => false, 'message' => 'Invalid Guest ID.'], 404);
    }

    return response()->json(['ok' => true, 'guest' => $guest]);
}

public function searchByName(Request $request)
{
    $guests = Guest::where('name', 'like', '%' . $request->name . '%')
                   ->get(['id', 'name']);

    if ($guests->isEmpty()) {
        return response()->json(['ok' => false, 'message' => 'No guests found.'], 404);
    }

    return response()->json(['ok' => true, 'guests' => $guests]);
}

public function find(Request $request)
{
    $guest = null;

    if ($request->filled('id')) {
        $guest = \App\Models\Guest::where('guest_id', $request->id)->first();
    } elseif ($request->filled('name')) {
        $guest = \App\Models\Guest::where('guest_name', 'like', $request->name)->first();
    }

    if ($guest) {
        return response()->json(['ok' => true, 'guest' => $guest]);
    }
    return response()->json(['ok' => false]);
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