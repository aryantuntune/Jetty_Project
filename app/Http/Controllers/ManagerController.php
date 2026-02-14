<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use App\Models\Route;
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

    /**
     * Get all routes with their branch names for dropdowns.
     */
    private function getRoutesForDropdown()
    {
        $routeEntries = Route::with('branch')->orderBy('route_id')->get();
        $grouped = $routeEntries->groupBy('route_id');

        $routes = [];
        foreach ($grouped as $routeId => $entries) {
            $branchNames = $entries->pluck('branch.branch_name')->filter()->values();
            $routes[] = [
                'route_id' => $routeId,
                'label' => $branchNames->join(' ↔ '),
                'branch_ids' => $entries->pluck('branch_id')->values(),
            ];
        }

        return $routes;
    }

    public function index()
    {
        $managers = User::where('role_id', 3)
            ->with(['branch', 'ferryboat'])
            ->paginate(10);

        // Attach route info to each manager
        $routeEntries = Route::with('branch')->get()->groupBy('route_id');
        $managers->getCollection()->transform(function ($manager) use ($routeEntries) {
            if ($manager->route_id && isset($routeEntries[$manager->route_id])) {
                $branchNames = $routeEntries[$manager->route_id]->pluck('branch.branch_name')->filter();
                $manager->route_label = $branchNames->join(' ↔ ');
            } else {
                $manager->route_label = $manager->branch?->branch_name ?? '—';
            }
            return $manager;
        });

        return Inertia::render('Manager/Index', ['managers' => $managers]);
    }

    public function create()
    {
        $routes = $this->getRoutesForDropdown();
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Manager/Create', [
            'routes' => $routes,
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
            'route_id' => 'required|integer',
        ]);

        // Get first branch on the route for backward compatibility
        $firstBranch = Route::where('route_id', $request->route_id)->first();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'branch_id' => $firstBranch?->branch_id,
            'route_id' => $request->route_id,
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

        $routes = $this->getRoutesForDropdown();
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return Inertia::render('Manager/Edit', [
            'manager' => $manager,
            'routes' => $routes,
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
            'route_id' => 'required|integer',
        ]);

        // Get first branch on the route for backward compatibility
        $firstBranch = Route::where('route_id', $request->route_id)->first();

        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'branch_id' => $firstBranch?->branch_id,
            'route_id' => $request->route_id,
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