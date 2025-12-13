<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ItemCategory;
use App\Models\ItemRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemRateController extends Controller
{

    public function __construct()
{
    // Protect only these actions
    $this->middleware(['auth', 'role:1,2'])->only(['create', 'store', 'update']);

    // (Optional) if you also want edit/destroy protected:
    // $this->middleware(['auth', 'role:1,2'])->only(['create','store','update','edit','destroy']);
}




    
   public function index(Request $request)
{
    $user = auth()->user();

    // Determine which branches to show in dropdown
    if (in_array($user->role_id, [1,2])) {
        // Admin/Manager: show all branches
        $branches = \App\Models\Branch::orderBy('branch_name')->get(['id','branch_name']);
        $branchQuery = $request->branch_id ? $request->branch_id : null;
    } else {
        // Branch user: only their branch
        $branches = \App\Models\Branch::where('id', $user->branch_id)->get(['id','branch_name']);
        $branchQuery = $user->branch_id; // restrict query automatically
    }

    $q = \App\Models\ItemRate::query()
        ->when($branchQuery, fn($qq) => $qq->where('branch_id', $branchQuery))
        ->when($request->item_category_id, fn($qq) => $qq->where('item_category_id', $request->item_category_id))
        ->when($request->search, fn($qq) => $qq->where('item_name','like', "%{$request->search}%"))
        ->orderByDesc('starting_date')
        ->orderBy('item_name');

    $itemRates = $q->paginate(15)->withQueryString();

    return view('item-rates.index', compact('itemRates','branches'));
}


    public function create()
    {
           $categories = \App\Models\ItemCategory::orderBy('category_name')->get(['id','category_name']);

    
    $routes = DB::table('routes')
        ->join('branches','routes.branch_id','=','branches.id')
        ->select('routes.route_id', DB::raw('GROUP_CONCAT(branches.branch_name ORDER BY branches.branch_name SEPARATOR " - ") as branch_names'),
                 DB::raw('GROUP_CONCAT(branches.id) as branch_ids'))
        ->groupBy('routes.route_id')
        ->get();

    return view('item-rates.create', compact('categories','routes'));
    }

   public function store(Request $request)
{
    
    $data = $request->validate([
        'item_name'        => ['required','string','max:150'],
        'item_category_id' => ['nullable','integer'],
        'item_rate'        => ['required','numeric','min:0'],
        'item_lavy'        => ['required','numeric','min:0'],
         'branch_id'        => ['required','array'],
        'starting_date'    => ['required','date'],
        'ending_date'      => ['nullable','date','after_or_equal:starting_date'],
        'item_id'         => ['required','integer'],
    ]);

    $userId = auth()->id();

    foreach ($data['branch_id'] as $branchId) {
        $exists = ItemRate::where('item_name', $data['item_name'])
            ->where('item_category_id', $data['item_category_id'] ?? null)
            ->where('branch_id', $branchId)
            ->where('starting_date', $data['starting_date'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['branch_id' => "Duplicate rate exists for Branch ID {$branchId} with same Item/Category/Start Date"]);
        }

        ItemRate::create([
            'item_name'        => $data['item_name'],
            'item_category_id' => $data['item_category_id'] ?? null,
            'item_rate'        => $data['item_rate'],
            'item_lavy'        => $data['item_lavy'],
            'branch_id'        => $branchId,
            'starting_date'    => $data['starting_date'],
            'ending_date'      => $data['ending_date'] ?? null,
            'user_id'          => $userId,
            'item_id'          => $data['item_id'],
        ]);
    }

    return redirect()->route('item-rates.index')->with('ok', 'Item rates added successfully.');
}

    public function edit(ItemRate $itemRate)
    {
        // $branches   = \App\Models\Branch::orderBy('branch_name')->get(['id','branch_name']);
    $categories = \App\Models\ItemCategory::orderBy('category_name')->get(['id','category_name']);
    $routes = DB::table('routes')
            ->join('branches','routes.branch_id','=','branches.id')
            ->select(
                'routes.route_id', 
                DB::raw('GROUP_CONCAT(branches.branch_name ORDER BY branches.branch_name SEPARATOR " - ") as branch_names'),
                DB::raw('GROUP_CONCAT(branches.id) as branch_ids')
            )
            ->groupBy('routes.route_id')
            ->get();
    return view('item-rates.edit', compact('itemRate','routes','categories'));

    }

public function update(Request $request, ItemRate $itemRate)
{
    $data = $request->validate([
        'item_name'        => ['required','string','max:150'],
        'item_category_id' => ['nullable','integer'],
        'item_rate'        => ['required','numeric','min:0'],
        'item_lavy'        => ['required','numeric','min:0'],
        'branch_id'        => ['required','array'], // array from selected route
        'starting_date'    => ['required','date'],
        'ending_date'      => ['nullable','date','after_or_equal:starting_date'],
    ]);

    $userId = auth()->id();
    $selectedBranches = $data['branch_id'];

    // Get all existing records for this item_id + date (without filtering by item_name)
    $existingRates = ItemRate::where('item_id', $itemRate->item_id)
        ->where('starting_date', $data['starting_date'])
        ->get();

    foreach ($selectedBranches as $branchId) {
        $existing = $existingRates->firstWhere('branch_id', $branchId);

        if ($existing) {
            // ✅ Update even if item_name or category changes
            $existing->update([
                'item_name'        => $data['item_name'],
                'item_category_id' => $data['item_category_id'] ?? null,
                'item_rate'        => $data['item_rate'],
                'item_lavy'        => $data['item_lavy'],
                'starting_date'    => $data['starting_date'],
                'ending_date'      => $data['ending_date'] ?? null,
                'user_id'          => $userId,
            ]);
        } else {
            // ✅ Avoid duplicate check on item_name, use item_id + branch + date instead
            $duplicate = ItemRate::where('item_id', $itemRate->item_id)
                ->where('starting_date', $data['starting_date'])
                ->where('branch_id', $branchId)
                ->exists();

            if ($duplicate) {
                return back()
                    ->withInput()
                    ->withErrors(['branch_id' => "Duplicate rate exists for Branch ID {$branchId}"]);
            }

            ItemRate::create([
                'item_name'        => $data['item_name'],
                'item_category_id' => $data['item_category_id'] ?? null,
                'item_rate'        => $data['item_rate'],
                'item_lavy'        => $data['item_lavy'],
                'branch_id'        => $branchId,
                'starting_date'    => $data['starting_date'],
                'ending_date'      => $data['ending_date'] ?? null,
                'user_id'          => $userId,
                'item_id'          => $itemRate->item_id,
            ]);
        }
    }

    return redirect()->route('item-rates.index')->with('ok', 'Item rates updated successfully.');
}





    public function destroy(ItemRate $itemRate)
    {
        $itemRate->delete();
        return redirect()->route('item-rates.index')->with('ok', 'Item rate deleted.');
    }
    public function show(ItemRate $itemRate)
{
    // simplest: reuse edit page or make a read-only view
    return view('item-rates.show', compact('itemRate'));
}

    // API Method for Mobile App
    public function getItemRatesByBranch($branchId)
    {
        $items = ItemRate::where('branch_id', $branchId)
            ->effective() // apply date filter
            ->select('id', 'item_name', 'item_rate', 'item_lavy')
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Item rates retrieved successfully',
            'data' => $items
        ]);
    }
}