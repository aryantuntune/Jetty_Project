<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FerrySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FerryScheduleController extends Controller
{

    public function __construct()
    {
        // Protect all actions except index and show
        $this->middleware(['auth', 'role:1,2'])->except(['index', 'show']);
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->query('branch_id');

        // Branch dropdown
        if (in_array($user->role_id, [1, 2])) {
            // Superadmin/admin: all branches
            $branches = Branch::all();
        } else {
            // Regular roles: only their branch
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        // Fetch schedules
        $schedules = FerrySchedule::with('branch')
            ->when(in_array($user->role_id, [3, 4]), fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        return view('ferry_schedules.index', compact('schedules', 'branches', 'branchId', 'user'));
    }

    public function create()
    {
        $user = Auth::user();

        // Limit branch options based on role
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('ferry_schedules.create', compact('branches', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'hour' => 'required|integer|min:0|max:23',
            'minute' => 'required|integer|min:0|max:59',
        ]);

        FerrySchedule::create($request->only(['branch_id', 'hour', 'minute']));

        return redirect()->route('ferry_schedules.index')->with('success', 'Ferry schedule added successfully.');
    }

    public function edit(FerrySchedule $ferry_schedule)
    {
        $user = Auth::user();

        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::all();
        } else {
            $branches = Branch::where('id', $user->branch_id)->get();
        }

        return view('ferry_schedules.edit', compact('ferry_schedule', 'branches', 'user'));
    }

    public function update(Request $request, FerrySchedule $ferry_schedule)
    {
        $request->validate([
            // 'branch_id' => 'required|exists:branches,id',
            'hour' => 'required|integer|min:0|max:23',
            'minute' => 'required|integer|min:0|max:59',
        ]);

        $ferry_schedule->update($request->only([ 'hour', 'minute']));

        return redirect()->route('ferry_schedules.index')->with('success', 'Ferry schedule updated successfully.');
    }

    public function destroy(FerrySchedule $ferry_schedule)
    {
        $ferry_schedule->delete();

        return redirect()->route('ferry_schedules.index')->with('success', 'Ferry schedule deleted successfully.');
    }
}