<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FerryBoat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FerryBoatController extends Controller
{
    public function __construct()
    {
        // Protect all actions except index and show
        $this->middleware(['auth', 'role:1,2'])->except(['index', 'show']);
    }
    public function index(Request $request)
    {
        $user = Auth::user();

        // Role 1 & 2 → see all branches
        if (in_array($user->role_id, [1,2])) {
            $branches = Branch::all();
            $branchId = $request->query('branch_id');
        } 
        // Role 3 → only their branch
        else {
            $branches = Branch::where('id', $user->branch_id)->get();
            $branchId = $user->branch_id;
        }

        $boats = FerryBoat::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $total = $boats->count();

        return view('ferryboats.index', compact('boats', 'total', 'branches', 'branchId'));
    }

    public function create()
    {
        $user = Auth::user();

        // Role 1 & 2 → all branches, Role 3 → only their branch
        $branches = in_array($user->role_id, [1,2]) 
            ? Branch::all() 
            : Branch::where('id', $user->branch_id)->get();

        return view('ferryboats.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'name'   => 'required',
            'branch_id'=>'required',
        ]);

        FerryBoat::create([
            'number'    => $request->number,
            'name'      => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        return redirect()->route('ferryboats.index')
                         ->with('success', 'Ferry Boat added successfully.');
    }

    public function edit(FerryBoat $ferryboat)
    {
        $user = Auth::user();
        $branches = in_array($user->role_id, [1,2]) 
            ? Branch::all() 
            : Branch::where('id', $user->branch_id)->get();

        return view('ferryboats.edit', compact('ferryboat','branches'));
    }

    public function update(Request $request, FerryBoat $ferryboat)
    {
        $request->validate([
            'number' => 'required',
            'name'   => 'required',
            'branch_id' => 'required',
        ]);

        $ferryboat->update($request->only('number','name','branch_id'));

        return redirect()->route('ferryboats.index')
                         ->with('success','Ferry Boat updated successfully.');
    }

    public function destroy(FerryBoat $ferryboat)
    {
        $ferryboat->delete();

        return redirect()->route('ferryboats.index')
                         ->with('success','Ferry Boat deleted successfully.');
    }
}