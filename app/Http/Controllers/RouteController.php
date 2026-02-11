<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RouteController extends Controller
{
    /**
     * Display a listing of routes
     */
    public function index()
    {
        // Group routes by route_id
        $routeGroups = Route::with('branch')
            ->orderBy('route_id')
            ->orderBy('sequence')
            ->get()
            ->groupBy('route_id');

        return Inertia::render('Masters/Routes/Index', ['routeGroups' => $routeGroups]);
    }

    /**
     * Show the form for creating a new route
     */
    public function create()
    {
        $branches = Branch::active()->orderBy('branch_name')->get();
        $nextRouteId = (Route::max('route_id') ?? 0) + 1;

        return Inertia::render('Masters/Routes/Create', [
            'branches' => $branches,
            'nextRouteId' => $nextRouteId,
        ]);
    }

    /**
     * Store a newly created route
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|integer|min:1',
            'branches' => 'required|array|min:2',
            'branches.*' => 'required|integer|exists:branches,id',
        ]);

        DB::transaction(function () use ($request) {
            $routeId = $request->route_id;

            // Delete existing entries for this route_id if editing
            Route::where('route_id', $routeId)->delete();

            // Create new entries
            foreach ($request->branches as $sequence => $branchId) {
                Route::create([
                    'route_id' => $routeId,
                    'branch_id' => $branchId,
                    'sequence' => $sequence + 1,
                ]);
            }
        });

        return redirect()->route('routes.index')
            ->with('success', 'Route created successfully!');
    }

    /**
     * Show the form for editing the specified route
     */
    public function edit($routeId)
    {
        $branches = Branch::active()->orderBy('branch_name')->get();
        $routeEntries = Route::where('route_id', $routeId)
            ->orderBy('sequence')
            ->get();

        if ($routeEntries->isEmpty()) {
            return redirect()->route('routes.index')
                ->with('error', 'Route not found');
        }

        return Inertia::render('Masters/Routes/Edit', [
            'branches' => $branches,
            'routeId' => $routeId,
            'routeEntries' => $routeEntries,
        ]);
    }

    /**
     * Update the specified route
     */
    public function update(Request $request, $routeId)
    {
        $request->validate([
            'branches' => 'required|array|min:2',
            'branches.*' => 'required|integer|exists:branches,id',
        ]);

        DB::transaction(function () use ($request, $routeId) {
            // Delete existing entries
            Route::where('route_id', $routeId)->delete();

            // Create new entries
            foreach ($request->branches as $sequence => $branchId) {
                Route::create([
                    'route_id' => $routeId,
                    'branch_id' => $branchId,
                    'sequence' => $sequence + 1,
                ]);
            }
        });

        return redirect()->route('routes.index')
            ->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified route
     */
    public function destroy($routeId)
    {
        Route::where('route_id', $routeId)->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Route deleted successfully!');
    }
}
