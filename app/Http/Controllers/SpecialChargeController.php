<?php

namespace App\Http\Controllers;

use App\Models\SpecialCharge;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpecialChargeController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();

        $charges = SpecialCharge::with('branch')
            ->when($request->branch_id, fn($q) => $q->where('branch_id', $request->branch_id))
            ->oldest()
            ->get();

        return Inertia::render('Masters/SpecialCharges/Index', [
            'charges' => $charges,
            'branches' => $branches,
        ]);
    }


    public function create()
    {
        $branches = Branch::all();
        return Inertia::render('Masters/SpecialCharges/Create', ['branches' => $branches]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'special_charge' => 'required|numeric|min:0',
        ]);

        // Check if a special charge already exists for this branch
        $exists = SpecialCharge::where('branch_id', $request->branch_id)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'A special charge for this branch already exists.');
        }

        SpecialCharge::create($validated);

        return redirect()
            ->route('special-charges.index')
            ->with('success', 'Special charge created successfully.');
    }


    public function edit(SpecialCharge $specialCharge)
    {
        $branches = Branch::all();
        return Inertia::render('Masters/SpecialCharges/Edit', [
            'specialCharge' => $specialCharge,
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, SpecialCharge $specialCharge)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'special_charge' => 'required|numeric|min:0',
        ]);

        $specialCharge->update($validated);

        return redirect()->route('special-charges.index')->with('success', 'Special charge updated successfully.');
    }

    public function destroy(SpecialCharge $specialCharge)
    {
        $specialCharge->delete();
        return redirect()->route('special-charges.index')->with('success', 'Special charge deleted successfully.');
    }
}