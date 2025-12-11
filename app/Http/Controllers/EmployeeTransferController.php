<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\BranchTransfer;
use Illuminate\Http\Request;

class EmployeeTransferController extends Controller
{

    public function transferPage()
{
    $users = User::with('branch')->get(); // get all employees
    return view('employees.transfer_index', compact('users'));
}


    public function showTransferForm($id)
    {
        $user = User::with('branch')->findOrFail($id);
        $branches = Branch::all();

        return view('employees.transfer', compact('user', 'branches'));
    }

    public function transfer(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'to_branch_id' => 'required|exists:branches,id|different:current_branch_id',
        ],[
            'to_branch_id.different' => 'The new branch must be different from current branch.'
        ]);

        $fromBranchId = $user->branch_id;
        $toBranchId   = $request->to_branch_id;

        // Update user table
        $user->update([
            'branch_id' => $toBranchId,
        ]);

        // Insert into branch_transfers table
        BranchTransfer::create([
            'user_id' => $user->id,
            'from_branch_id' => $fromBranchId,
            'to_branch_id' => $toBranchId,
        ]);

       return redirect()->route('employees.transfer.index')
                     ->with('success', 'Employee transferred successfully.');
    }
}