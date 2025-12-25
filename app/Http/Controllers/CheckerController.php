<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CheckerController extends Controller
{
    public function __construct()
    {
        // Allow Super Admin (1), Admin (2), and Manager (3)
        $this->middleware(['auth', 'role:1,2,3']);
    }

    /**
     * Get the base query filtered by role
     * - Super Admin/Admin: see all checkers
     * - Manager: see only checkers on their ferry route
     */
    private function getFilteredQuery()
    {
        $user = Auth::user();
        $query = User::where('role_id', 5)->with(['branch', 'ferryboat']);

        // Manager can only see checkers assigned to their ferry route
        if ($user->role_id == 3) {
            $query->where('ferry_boat_id', $user->ferry_boat_id);
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

        // Manager can only manage checkers on their route
        if ($user->role_id == 3) {
            return $checker->ferry_boat_id == $user->ferry_boat_id;
        }

        return false;
    }

    public function index()
    {
        $checkers = $this->getFilteredQuery()->paginate(10);
        $isManager = Auth::user()->role_id == 3;

        return view('checker.index', compact('checkers', 'isManager'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role_id == 3) {
            // Manager can only create checkers for their route
            $branches = Branch::all();
            $ferryboats = FerryBoat::where('id', $user->ferry_boat_id)->get();
        } else {
            $branches = Branch::all();
            $ferryboats = FerryBoat::all();
        }

        return view('checker.create', compact('branches', 'ferryboats'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'mobile'      => 'nullable|string|max:20',
            'branch_id'   => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        // Manager can only create checkers for their route
        $ferryBoatId = $request->ferryboat_id;
        if ($user->role_id == 3) {
            $ferryBoatId = $user->ferry_boat_id;
        }

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'mobile'      => $request->mobile,
            'branch_id'   => $request->branch_id,
            'ferry_boat_id' => $ferryBoatId,
            'role_id'     => 5,  // CHECKER ROLE
        ]);

        return redirect()->route('checker.index')->with('success', 'Checker created successfully!');
    }

    public function show(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        return view('checker.show', compact('checker'));
    }

    public function edit(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        $user = Auth::user();

        if ($user->role_id == 3) {
            // Manager can only assign to their route
            $branches = Branch::all();
            $ferryboats = FerryBoat::where('id', $user->ferry_boat_id)->get();
        } else {
            $branches = Branch::all();
            $ferryboats = FerryBoat::all();
        }

        return view('checker.edit', compact('checker', 'branches', 'ferryboats'));
    }

    public function update(Request $request, User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        abort_if(!$this->canManage($checker), 403);

        $user = Auth::user();

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $checker->id,
            'password'    => 'nullable|string|min:6',
            'mobile'      => 'nullable|string|max:20',
            'branch_id'   => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        // Manager can only assign to their route
        $ferryBoatId = $request->ferryboat_id;
        if ($user->role_id == 3) {
            $ferryBoatId = $user->ferry_boat_id;
        }

        $checker->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'branch_id'   => $request->branch_id,
            'ferry_boat_id' => $ferryBoatId,
            'role_id'     => 5,
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
