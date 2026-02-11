<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ManagerController extends Controller
{
    public function __construct()
    {
        // Protect all actions
        $this->middleware(['auth', 'role:1,2']);
    }
    public function index()
    {
        $managers = User::where('role_id', 3)
            ->with(['branch', 'ferryboat'])
            ->paginate(10);

        return Inertia::render('Manager/Index', ['managers' => $managers]);
    }

    public function create()
    {
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Manager/Create', [
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable',
            'ferry_boat_id' => 'nullable',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id,
            'role_id' => 3, // Manager role
            'role' => 'Manager',
        ]);

        return redirect()->route('manager.index')->with('success', 'Manager created successfully!');
    }

    public function show(User $manager)
    {
        abort_if($manager->role_id != 3, 404);
        return Inertia::render('Manager/Show', ['manager' => $manager->load(['branch', 'ferryboat'])]);
    }

    public function edit(User $manager)
    {
        abort_if($manager->role_id != 3, 404);

        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Manager/Edit', [
            'manager' => $manager,
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function update(Request $request, User $manager)
    {
        abort_if($manager->role_id != 3, 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $manager->id,
            'password' => 'nullable|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id,
            'role_id' => 3,
            'role' => 'Manager',
        ]);

        if ($request->filled('password')) {
            $manager->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('manager.index')->with('success', 'Manager updated successfully!');
    }

    public function destroy(User $manager)
    {
        abort_if($manager->role_id != 3, 404);

        $manager->delete();

        return redirect()->route('manager.index')->with('success', 'Manager deleted successfully!');
    }
}