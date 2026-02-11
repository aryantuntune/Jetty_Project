<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function __construct()
    {
        // Protect all actions except index, show, and getBranches (API method)
        $this->middleware(['auth', 'role:1,2'])->except(['index', 'show', 'getBranches']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Branch::query();

        // Filter by branch_id & branch_name
        if ($request->filled('branch_id')) {
            $query->where('branch_id', 'like', "%{$request->branch_id}%");
        }
        if ($request->filled('branch_name')) {
            $query->where('branch_name', 'like', "%{$request->branch_name}%");
        }

        $branches = $query->get();
        $total = $branches->count();

        return Inertia::render('Masters/Branches/Index', [
            'branches' => $branches,
            'total' => $total,
            'user' => $user,
        ]);
    }


    public function create()
    {
        return Inertia::render('Masters/Branches/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|unique:branches,branch_id',
            'branch_name' => 'required',
        ]);

        Branch::create($request->only(['branch_id', 'branch_name']));

        return redirect()->route('branches.index')->with('success', 'Branch added successfully!');
    }

    public function edit(Branch $branch)
    {
        return Inertia::render('Masters/Branches/Edit', ['branch' => $branch]);
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'branch_id' => 'required|unique:branches,branch_id,' . $branch->id,
            'branch_name' => 'required',
        ]);

        $branch->update($request->only(['branch_id', 'branch_name']));

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully!');
    }

    // API Methods for Mobile App
    public function getBranches()
    {
        $branches = Branch::select('id', 'branch_name as name')
            ->orderBy('branch_name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Branches retrieved successfully',
            'data' => $branches
        ]);
    }
}