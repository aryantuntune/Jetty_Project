<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ItemCategory;
use App\Models\ItemRate;
use App\Helpers\DbHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

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
        if (in_array($user->role_id, [1, 2])) {
            // Admin/Manager: show all branches
            $branches = \App\Models\Branch::orderBy('branch_name')->get(['id', 'branch_name']);
            $branchQuery = $request->branch_id ? $request->branch_id : null;
        } else {
            // Branch user: only their branch
            $branches = \App\Models\Branch::where('id', $user->branch_id)->get(['id', 'branch_name']);
            $branchQuery = $user->branch_id; // restrict query automatically
        }

        // Get categories for filter dropdown
        $categories = \App\Models\ItemCategory::orderBy('category_name')->get(['id', 'category_name']);

        $q = \App\Models\ItemRate::with(['branch', 'category'])
            ->when($branchQuery, fn($qq) => $qq->where('branch_id', $branchQuery))
            ->when($request->item_category_id, fn($qq) => $qq->where('item_category_id', $request->item_category_id))
            ->when($request->search, fn($qq) => $qq->where('item_name', 'like', "%{$request->search}%"))
            ->when($request->is_vehicle !== null && $request->is_vehicle !== '', fn($qq) => $qq->where('is_vehicle', $request->is_vehicle))
            ->orderBy('branch_id')
            ->orderBy('item_category_id')
            ->orderBy('item_name')
            ->orderByDesc('item_rate');

        $itemRates = $q->paginate(25)->withQueryString();

        return Inertia::render('Masters/ItemRates/Index', [
            'itemRates' => $itemRates,
            'branches' => $branches,
            'categories' => $categories,
        ]);
    }


    public function create()
    {
        $user = auth()->user();
        $categories = \App\Models\ItemCategory::orderBy('category_name')->get(['id', 'category_name']);

        // Role-based branch filtering
        // Admin/SuperAdmin (role 1,2) see all branches
        // Manager (role 3) sees only their branch
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::orderBy('branch_name')->get(['id', 'branch_name']);
        } else {
            // Manager sees their branch only
            $branches = Branch::where('id', $user->branch_id)
                ->orderBy('branch_name')
                ->get(['id', 'branch_name']);
        }

        return Inertia::render('Masters/ItemRates/Create', [
            'categories' => $categories,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'item_category_id' => ['nullable', 'integer'],
            'item_rate' => ['required', 'numeric', 'min:0'],
            'item_lavy' => ['required', 'numeric', 'min:0'],
            'branch_id' => ['required', 'array'],
            'starting_date' => ['required', 'date'],
            'ending_date' => ['nullable', 'date', 'after_or_equal:starting_date'],
            'is_vehicle' => ['nullable', 'boolean'],
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
                'item_name' => $data['item_name'],
                'item_category_id' => $data['item_category_id'] ?? null,
                'item_rate' => $data['item_rate'],
                'item_lavy' => $data['item_lavy'],
                'branch_id' => $branchId,
                'starting_date' => $data['starting_date'],
                'ending_date' => $data['ending_date'] ?? null,
                'user_id' => $userId,
                'is_vehicle' => $request->boolean('is_vehicle'),
                'is_active' => true,
                'created_by' => $userId,
            ]);
        }

        return redirect()->route('item-rates.index')->with('ok', 'Item rates added successfully.');
    }

    public function edit(ItemRate $itemRate)
    {
        $user = auth()->user();
        $categories = \App\Models\ItemCategory::orderBy('category_name')->get(['id', 'category_name']);

        // Role-based branch filtering
        // Admin/SuperAdmin (role 1,2) see all branches
        // Manager (role 3) sees only their branch
        if (in_array($user->role_id, [1, 2])) {
            $branches = Branch::orderBy('branch_name')->get(['id', 'branch_name']);
        } else {
            // Manager sees their branch only
            $branches = Branch::where('id', $user->branch_id)
                ->orderBy('branch_name')
                ->get(['id', 'branch_name']);
        }

        return Inertia::render('Masters/ItemRates/Edit', [
            'itemRate' => $itemRate,
            'branches' => $branches,
            'categories' => $categories,
        ]);

    }

    public function update(Request $request, ItemRate $itemRate)
    {
        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:150'],
            'item_category_id' => ['nullable', 'integer'],
            'item_rate' => ['required', 'numeric', 'min:0'],
            'item_lavy' => ['required', 'numeric', 'min:0'],
            'branch_id' => ['required', 'array'],
            'starting_date' => ['required', 'date'],
            'ending_date' => ['nullable', 'date', 'after_or_equal:starting_date'],
            'is_vehicle' => ['nullable', 'boolean'],
        ]);

        $userId = auth()->id();
        $selectedBranches = $data['branch_id'];

        // Get all existing records for this item_name + date
        $existingRates = ItemRate::where('item_name', $itemRate->item_name)
            ->where('starting_date', $data['starting_date'])
            ->get();

        foreach ($selectedBranches as $branchId) {
            $existing = $existingRates->firstWhere('branch_id', $branchId);

            if ($existing) {
                $existing->update([
                    'item_name' => $data['item_name'],
                    'item_category_id' => $data['item_category_id'] ?? null,
                    'item_rate' => $data['item_rate'],
                    'item_lavy' => $data['item_lavy'],
                    'starting_date' => $data['starting_date'],
                    'ending_date' => $data['ending_date'] ?? null,
                    'user_id' => $userId,
                    'is_vehicle' => $request->boolean('is_vehicle'),
                    'updated_by' => $userId,
                ]);
            } else {
                $duplicate = ItemRate::where('item_name', $data['item_name'])
                    ->where('starting_date', $data['starting_date'])
                    ->where('branch_id', $branchId)
                    ->exists();

                if ($duplicate) {
                    return back()
                        ->withInput()
                        ->withErrors(['branch_id' => "Duplicate rate exists for Branch ID {$branchId}"]);
                }

                ItemRate::create([
                    'item_name' => $data['item_name'],
                    'item_category_id' => $data['item_category_id'] ?? null,
                    'item_rate' => $data['item_rate'],
                    'item_lavy' => $data['item_lavy'],
                    'branch_id' => $branchId,
                    'starting_date' => $data['starting_date'],
                    'ending_date' => $data['ending_date'] ?? null,
                    'user_id' => $userId,
                    'is_vehicle' => $request->boolean('is_vehicle'),
                    'is_active' => true,
                    'created_by' => $userId,
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
        return Inertia::render('Masters/ItemRates/Show', ['itemRate' => $itemRate]);
    }

    // API Method for Mobile App
    public function getItemRatesByBranch($branchId)
    {
        $items = ItemRate::where('branch_id', $branchId)
            ->effective() // apply date filter
            ->select('id', 'item_name', 'item_rate', 'item_lavy')
            ->orderBy('item_name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'item_rate' => floatval($item->item_rate),
                    'item_lavy' => floatval($item->item_lavy),
                    'price' => floatval($item->item_rate) + floatval($item->item_lavy), // Combined for convenience
                    'description' => 'Rate: ₹' . $item->item_rate . ' + Levy: ₹' . $item->item_lavy,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Item rates retrieved successfully',
            'data' => $items
        ]);
    }
}