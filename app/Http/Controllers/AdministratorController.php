<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdministratorController extends Controller
{
    public function __construct()
    {
        // Admins and SuperAdmin can access
        $this->middleware(['auth', 'role:1,2']);
    }
    public function index()
    {
        $query = User::with(['branch', 'ferryboat']);

        // Only show superadmin to themselves
        if ((int) auth()->user()->role_id === 1) {
            $query->whereIn('role_id', [1, 2]);
        } else {
            $query->where('role_id', 2); // Hide superadmin from admins
        }

        $administrators = $query->paginate(10);

        return Inertia::render('Admin/Index', ['administrators' => $administrators]);
    }

    public function create()
    {
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Admin/Create', [
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
            'role_id' => 2,
            'role' => 'Administrator',
        ]);

        return redirect()->route('admin.index')->with('success', 'Administrator created successfully!');
    }

    public function show(User $admin)
    {
        abort_if(!in_array($admin->role_id, [1, 2]), 404);
        // Hide superadmin from non-superadmin users
        abort_if($admin->role_id == 1 && (int) auth()->user()->role_id !== 1, 404);
        return Inertia::render('Admin/Show', ['admin' => $admin->load(['branch', 'ferryboat'])]);
    }

    public function edit(User $admin)
    {
        abort_if(!in_array($admin->role_id, [1, 2]), 404);
        // Hide superadmin from non-superadmin users
        abort_if($admin->role_id == 1 && (int) auth()->user()->role_id !== 1, 404);

        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Admin/Edit', [
            'admin' => $admin,
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function update(Request $request, User $admin)
    {
        abort_if(!in_array($admin->role_id, [1, 2]), 404);
        // Prevent non-superadmin from modifying superadmin
        abort_if($admin->role_id == 1 && (int) auth()->user()->role_id !== 1, 404);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'ferryboat_id' => 'nullable|exists:ferryboats,id',
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferryboat_id' => $request->ferryboat_id,
            'role_id' => 2,
        ]);

        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.index')->with('success', 'Administrator updated successfully!');
    }

    public function destroy(User $admin)
    {
        abort_if(!in_array($admin->role_id, [1, 2]), 404);
        // Prevent non-superadmin from even seeing or deleting superadmin
        abort_if($admin->role_id == 1 && (int) auth()->user()->role_id !== 1, 404);

        // Prevent deletion of superadmin (role_id = 1)
        if ($admin->role_id == 1) {
            return redirect()->route('admin.index')->with('error', 'Cannot delete Super Admin!');
        }

        $admin->delete();

        return redirect()->route('admin.index')->with('success', 'Administrator deleted successfully!');
    }
}