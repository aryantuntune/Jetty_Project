<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckerController extends Controller
{
    public function __construct()
    {
        // Allow Super Admin (1), Admin (2), and Manager (3)
        $this->middleware(['auth', 'role:1,2,3']);
    }

    /**
     * Get the branch IDs the current manager's route covers.
     */
    private function getManagerBranchIds()
    {
        $user = Auth::user();
        if ($user->role_id != 3)
            return null;
        return $user->getRouteBranchIds();
    }

    /**
     * Get the base query filtered by role
     * - Super Admin/Admin: see all checkers
     * - Manager: see only checkers at branches on their route
     */
    private function getFilteredQuery()
    {
        $query = User::where('role_id', 5)->with(['branch', 'ferryboat']);

        $branchIds = $this->getManagerBranchIds();
        if ($branchIds !== null) {
            $query->whereIn('branch_id', $branchIds);
        }

        return $query;
    }

    /**
     * Check if current user can manage this checker
     */
    private function canManage(User $checker)
    {
        $user = Auth::user();

        // Super Admin and Admin can manage any checker
        if (in_array($user->role_id, [1, 2])) {
            return true;
        }

        // Manager can only manage checkers on their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            return $branchIds->contains($checker->branch_id);
        }

        return false;
    }

    public function index()
    {
        $checkers = $this->getFilteredQuery()->paginate(10);
        $isManager = Auth::user()->role_id == 3;

        return Inertia::render('Checker/Index', [
            'checkers' => $checkers,
            'isManager' => $isManager,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role_id == 3) {
            // Manager can only create checkers for branches on their route
            $branchIds = $user->getRouteBranchIds();
            $branches = Branch::whereIn('id', $branchIds)->get();
            $ferryboats = FerryBoat::whereIn('branch_id', $branchIds)->get();
            if ($ferryboats->isEmpty()) {
                $ferryboats = FerryBoat::all();
            }
        } else {
            $branches = Branch::all();
            $ferryboats = FerryBoat::all();
        }

        return Inertia::render('Checker/Create', [
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        // Manager can only create checkers for their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            if ($request->branch_id && !$branchIds->contains($request->branch_id)) {
                abort(403, 'You can only create checkers for branches on your route.');
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id ?? $request->ferry_boat_id,
            'role_id' => 5,  // CHECKER ROLE
        ]);

        return redirect()->route('checker.index')->with('success', 'Checker created successfully!');
    }

    public function show(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        return Inertia::render('Checker/Show', ['checker' => $checker->load(['branch', 'ferryboat'])]);
    }

    public function edit(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        $user = Auth::user();

        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            $branches = Branch::whereIn('id', $branchIds)->get();
            $ferryboats = FerryBoat::whereIn('branch_id', $branchIds)->get();
            if ($ferryboats->isEmpty()) {
                $ferryboats = FerryBoat::all();
            }
        } else {
            $branches = Branch::all();
            $ferryboats = FerryBoat::all();
        }

        return Inertia::render('Checker/Edit', [
            'checker' => $checker,
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function update(Request $request, User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $checker->id,
            'password' => 'nullable|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        // Manager can only assign to their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            if ($request->branch_id && !$branchIds->contains($request->branch_id)) {
                abort(403, 'You can only assign checkers to branches on your route.');
            }
        }

        $checker->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id ?? $request->ferry_boat_id,
            'role_id' => 5,
        ]);

        if ($request->filled('password')) {
            $checker->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('checker.index')->with('success', 'Checker updated successfully!');
    }

    public function destroy(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        $checker->delete();

        return redirect()->route('checker.index')->with('success', 'Checker deleted successfully!');
    }
}
