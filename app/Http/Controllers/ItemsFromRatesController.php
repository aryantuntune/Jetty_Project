<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemsFromRatesController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();

    // Fetch branches depending on role
    if (in_array($user->role_id, [1,2])) {
        // Admin / Manager → all branches
        $branches = Branch::orderBy('branch_name')->get();
    } else {
        // Role 3 → only user’s branch
        $branches = Branch::where('id', $user->branch_id)
                          ->orderBy('branch_name')
                          ->get();
    }

    // Build query
    $q = DB::table('item_rates as ir')
        ->leftJoin('item_categories as ic', 'ic.id', '=', 'ir.item_category_id')
        ->leftJoin('branches as b', 'b.id', '=', 'ir.branch_id')
        ->selectRaw('DISTINCT ir.item_id, ir.item_name, ir.item_category_id, ic.category_name as category_name, b.branch_name')
        ->when($request->id, function ($qq) use ($request) {
            $qq->where('ir.item_id', $request->id);
        })
        ->when($request->name, function ($qq) use ($request) {
            $qq->where('ir.item_name', 'like', '%'.$request->name.'%');
        })
        ->when($request->branch_id, function ($qq) use ($request) {
            $qq->where('ir.branch_id', $request->branch_id);
        });

    // If role = 3, enforce branch restriction
    if ($user->role_id == 3) {
        $q->where('ir.branch_id', $user->branch_id);
    }

    $items = $q->orderBy('ir.item_id')->paginate(20)->withQueryString();

    return view('items.from_rates_index', compact('items', 'branches'));
}

}