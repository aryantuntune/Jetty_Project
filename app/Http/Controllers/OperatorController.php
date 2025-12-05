<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
    public function __construct()
    {
        // Protect all actions
        $this->middleware(['auth', 'role:1,2']);
    }
    public function index()
    {
        $operators = User::where('role_id', 4)
            ->with(['branch', 'ferryboat'])
            ->paginate(10);

        return view('operator.index', compact('operators'));
    }

    public function create()
    {
        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return view('operator.create', compact('branches', 'ferryboats'));
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
            'role_id'     => 4, // Operator role
        ]);

        return redirect()->route('operator.index')->with('success', 'Operator created successfully!');
    }

    public function show(User $operator)
    {
        abort_if($operator->role_id != 4, 404);
        return view('operator.show', compact('operator'));
    }

    public function edit(User $operator)
    {
        abort_if($operator->role_id != 4, 404);

        $branches = Branch::all();
        $ferryboats = FerryBoat::all();

        return view('operator.edit', compact('operator', 'branches', 'ferryboats'));
    }

    public function update(Request $request, User $operator)
    {
        abort_if($operator->role_id != 4, 404);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $operator->id,
            'password'    => 'nullable|string|min:6',
            'mobile'      => 'nullable|string|max:20',
            'branch_id'   => 'nullable|exists:branches,id',
            'ferry_boat_id' => 'nullable|exists:ferryboats,id',
        ]);

        $operator->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'mobile'      => $request->mobile,
            'branch_id'   => $request->branch_id,
            'ferry_boat_id' => $request->ferryboat_id,
            'role_id'     => 4,
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

        $operator->delete();

        return redirect()->route('operator.index')->with('success', 'Operator deleted successfully!');
    }
}
