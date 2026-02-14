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

class OperatorController extends Controller
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
     * - Super Admin/Admin: see all operators
     * - Manager: see only operators at branches on their route
     */
    private function getFilteredQuery()
    {
        $query = User::where('role_id', 4)->with(['branch', 'ferryboat']);

        $branchIds = $this->getManagerBranchIds();
        if ($branchIds !== null) {
            $query->whereIn('branch_id', $branchIds);
        }

        return $query;
    }

    /**
     * Check if current user can manage this operator
     */
    private function canManage(User $operator)
    {
        $user = Auth::user();

        // Super Admin and Admin can manage any operator
        if (in_array($user->role_id, [1, 2])) {
            return true;
        }

        // Manager can only manage operators on their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            return $branchIds->contains($operator->branch_id);
        }

        return false;
    }

    public function index()
    {
        $operators = $this->getFilteredQuery()->paginate(10);
        $isManager = Auth::user()->role_id == 3;

        return Inertia::render('Operator/Index', [
            'operators' => $operators,
            'isManager' => $isManager,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role_id == 3) {
            // Manager can only create operators for branches on their route
            $branchIds = $user->getRouteBranchIds();
            $branches = Branch::whereIn('id', $branchIds)->get();
            // Get ferryboats assigned to those branches
            $ferryboats = FerryBoat::whereIn('branch_id', $branchIds)->get();
            // If no ferryboats found by branch, get all for now
            if ($ferryboats->isEmpty()) {
                $ferryboats = FerryBoat::all();
            }
        } else {
            $branches = Branch::all();
            $ferryboats = FerryBoat::all();
        }

        return Inertia::render('Operator/Create', [
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

        // Manager can only create operators for their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            if ($request->branch_id && !$branchIds->contains($request->branch_id)) {
                abort(403, 'You can only create operators for branches on your route.');
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id ?? $request->ferry_boat_id,
            'role_id' => 4, // Operator role
        ]);

        return redirect()->route('operator.index')->with('success', 'Operator created successfully!');
    }

    public function show(User $operator)
    {
        abort_if($operator->role_id != 4, 404);
        abort_if(!$this->canManage($operator), 403);

        return Inertia::render('Operator/Show', ['operator' => $operator->load(['branch', 'ferryboat'])]);
    }

    public function edit(User $operator)
    {
        abort_if($operator->role_id != 4, 404);
        abort_if(!$this->canManage($operator), 403);

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

        return Inertia::render('Operator/Edit', [
            'operator' => $operator,
            'branches' => $branches,
            'ferryboats' => $ferryboats,
        ]);
    }

    public function update(Request $request, User $operator)
    {
        abort_if($operator->role_id != 4, 404);
        abort_if(!$this->canManage($operator), 403);

        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $operator->id,
            'password' => 'nullable|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        // Manager can only assign to their route's branches
        if ($user->role_id == 3) {
            $branchIds = $user->getRouteBranchIds();
            if ($request->branch_id && !$branchIds->contains($request->branch_id)) {
                abort(403, 'You can only assign operators to branches on your route.');
            }
        }

        $operator->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'branch_id' => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id ?? $request->ferry_boat_id,
            'role_id' => 4,
        ]);

        if ($request->filled('password')) {
            $operator->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('operator.index')->with('success', 'Operator updated successfully!');
    }

    public function destroy(User $operator)
    {
        abort_if($operator->role_id != 4, 404);
        abort_if(!$this->canManage($operator), 403);

        $operator->delete();

        return redirect()->route('operator.index')->with('success', 'Operator deleted successfully!');
    }
}
