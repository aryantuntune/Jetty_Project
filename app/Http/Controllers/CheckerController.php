<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckerController extends Controller
{
    public function __construct()
    {
        // Allow only Admin / SuperAdmin
        $this->middleware(['auth', 'role:1,2']);
    }

    public function index()
    {
        $checkers = User::where('role_id', 5)   // role_id = 5 for Checker
            ->with(['branch', 'ferryboat'])
            ->paginate(10);

        return view('checker.index', compact('checkers'));
    }

    public function create()
    {
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();
        return view('checker.create', compact('branches', 'ferryboats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'mobile'      => 'nullable|string|max:20',
            'branch_id'   => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'mobile'      => $request->mobile,
            'branch_id'   => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id,
            'role_id'     => 5,  // CHECKER ROLE
        ]);

        return redirect()->route('checker.index')->with('success', 'Checker created successfully!');
    }

    public function show(User $checker)
    {
        abort_if($checker->role_id != 5, 404);
        return view('checker.show', compact('checker'));
    }

    public function edit(User $checker)
    {
        abort_if($checker->role_id != 5, 404);

        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return view('checker.edit', compact('checker', 'branches', 'ferryboats'));
    }

    public function update(Request $request, User $checker)
    {
        abort_if($checker->role_id != 5, 404);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $checker->id,
            'password'    => 'nullable|string|min:6',
            'mobile'      => 'nullable|string|max:20',
            'branch_id'   => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        $checker->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'branch_id'   => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id,
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

        $checker->delete();

        return redirect()->route('checker.index')->with('success', 'Checker deleted successfully!');
    }
}
