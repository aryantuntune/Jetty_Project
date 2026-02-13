<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CheckerAuthController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerAuth\CustomerProfileController;
use App\Http\Controllers\CustomerAuth\ForgotPasswordController;
use App\Http\Controllers\CustomerAuth\LoginController;
use App\Http\Controllers\CustomerAuth\RegisterController;
use App\Http\Controllers\FerryBoatController;
use App\Http\Controllers\ItemRateController;
use App\Http\Controllers\RazorpayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// ADMIN API AUTH (for React Admin Panel)
// ============================================
Route::prefix('admin')->group(function () {
    // Login (Smart Rate Limited: 5 attempts per minute per email+IP)
    Route::middleware('throttle:login')->post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'role_name' => $user->role->role_name ?? 'Admin',
                    'branch_id' => $user->branch_id,
                    'branch_name' => $user->branch->branch_name ?? null,
                ],
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401);
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully']);
    })->middleware('auth:sanctum');

    // Get current user
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role->role_name ?? 'Admin',
            'branch_id' => $user->branch_id,
            'branch_name' => $user->branch->branch_name ?? null,
        ]);
    })->middleware('auth:sanctum');
});

// ============================================
// MASTER DATA API ROUTES (for React Admin Panel)
// ============================================

// BRANCHES - Full CRUD
Route::prefix('admin/branches')->group(function () {
    // List all branches (auth required)
    Route::get('/', function () {
        $branches = \App\Models\Branch::orderBy('branch_name')->get();
        return response()->json(['success' => true, 'data' => $branches]);
    })->middleware('auth:sanctum');

    // Get single branch (auth required)
    Route::get('/{id}', function ($id) {
        $branch = \App\Models\Branch::findOrFail($id);
        return response()->json(['success' => true, 'data' => $branch]);
    })->middleware('auth:sanctum');

    // Create branch (auth required)
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'branch_id' => 'required|unique:branches,branch_id',
            'branch_name' => 'required|string|max:100',
        ]);
        $branch = \App\Models\Branch::create($data);
        return response()->json(['success' => true, 'data' => $branch, 'message' => 'Branch created']);
    })->middleware('auth:sanctum');

    // Update branch (auth required)
    Route::put('/{id}', function (Request $request, $id) {
        $branch = \App\Models\Branch::findOrFail($id);
        $data = $request->validate([
            'branch_id' => 'required|unique:branches,branch_id,' . $id,
            'branch_name' => 'required|string|max:100',
        ]);
        $branch->update($data);
        return response()->json(['success' => true, 'data' => $branch, 'message' => 'Branch updated']);
    })->middleware('auth:sanctum');

    // Delete branch (auth required)
    Route::delete('/{id}', function ($id) {
        $branch = \App\Models\Branch::findOrFail($id);
        $branch->delete();
        return response()->json(['success' => true, 'message' => 'Branch deleted']);
    })->middleware('auth:sanctum');
});

// FERRIES - Full CRUD
Route::prefix('admin/ferries')->group(function () {
    // List all ferries (auth required)
    Route::get('/', function (Request $request) {
        $query = \App\Models\FerryBoat::with('branch');
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $ferries = $query->orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $ferries]);
    })->middleware('auth:sanctum');

    // Get single ferry (auth required)
    Route::get('/{id}', function ($id) {
        $ferry = \App\Models\FerryBoat::with('branch')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $ferry]);
    })->middleware('auth:sanctum');

    // Create ferry (auth required)
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'number' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $ferry = \App\Models\FerryBoat::create($data);
        return response()->json(['success' => true, 'data' => $ferry, 'message' => 'Ferry created']);
    })->middleware('auth:sanctum');

    // Update ferry (auth required)
    Route::put('/{id}', function (Request $request, $id) {
        $ferry = \App\Models\FerryBoat::findOrFail($id);
        $data = $request->validate([
            'number' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $ferry->update($data);
        return response()->json(['success' => true, 'data' => $ferry, 'message' => 'Ferry updated']);
    })->middleware('auth:sanctum');

    // Delete ferry (auth required)
    Route::delete('/{id}', function ($id) {
        $ferry = \App\Models\FerryBoat::findOrFail($id);
        $ferry->delete();
        return response()->json(['success' => true, 'message' => 'Ferry deleted']);
    })->middleware('auth:sanctum');
});

// RATES - Full CRUD
Route::prefix('admin/rates')->group(function () {
    // List all rates (auth required)
    Route::get('/', function (Request $request) {
        $query = \App\Models\ItemRate::with('branch');
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $rates = $query->orderBy('item_name')->get();
        return response()->json(['success' => true, 'data' => $rates]);
    })->middleware('auth:sanctum');

    // Get single rate (auth required)
    Route::get('/{id}', function ($id) {
        $rate = \App\Models\ItemRate::with('branch')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $rate]);
    })->middleware('auth:sanctum');

    // Create rate (auth required)
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'item_name' => 'required|string|max:100',
            'item_rate' => 'required|numeric|min:0',
            'item_lavy' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $rate = \App\Models\ItemRate::create($data);
        return response()->json(['success' => true, 'data' => $rate, 'message' => 'Rate created']);
    })->middleware('auth:sanctum');

    // Update rate (auth required)
    Route::put('/{id}', function (Request $request, $id) {
        $rate = \App\Models\ItemRate::findOrFail($id);
        $data = $request->validate([
            'item_name' => 'required|string|max:100',
            'item_rate' => 'required|numeric|min:0',
            'item_lavy' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $rate->update($data);
        return response()->json(['success' => true, 'data' => $rate, 'message' => 'Rate updated']);
    })->middleware('auth:sanctum');

    // Delete rate (auth required)
    Route::delete('/{id}', function ($id) {
        $rate = \App\Models\ItemRate::findOrFail($id);
        $rate->delete();
        return response()->json(['success' => true, 'message' => 'Rate deleted']);
    })->middleware('auth:sanctum');
});

// USERS - Full CRUD
Route::prefix('admin/users')->group(function () {
    // List all users (with optional role_id and branch_id filter) - AUTH REQUIRED
    Route::get('/', function (Request $request) {
        $query = \App\Models\User::with('branch');
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $users = $query->orderBy('name')->get()->map(function ($u) {
            $roleNames = [1 => 'Super Admin', 2 => 'Admin', 3 => 'Manager', 4 => 'Operator', 5 => 'Checker'];
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'mobile' => $u->mobile ?? null,
                'role_id' => $u->role_id,
                'role_name' => $roleNames[$u->role_id] ?? 'User',
                'branch_id' => $u->branch_id,
                'branch_name' => $u->branch->branch_name ?? null,
                'created_at' => $u->created_at->format('Y-m-d'),
            ];
        });
        return response()->json(['success' => true, 'data' => $users]);
    })->middleware('auth:sanctum');

    // Get single user (auth required)
    Route::get('/{id}', function ($id) {
        $user = \App\Models\User::with('branch')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $user]);
    })->middleware('auth:sanctum');

    // Create user (auth required)
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|in:1,2,3',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        $data['password'] = bcrypt($data['password']);
        $user = \App\Models\User::create($data);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'User created']);
    })->middleware('auth:sanctum');

    // Update user (auth required)
    Route::put('/{id}', function (Request $request, $id) {
        $user = \App\Models\User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|in:1,2,3',
            'branch_id' => 'nullable|exists:branches,id',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['success' => true, 'data' => $user, 'message' => 'User updated']);
    })->middleware('auth:sanctum');

    // Delete user (auth required)
    Route::delete('/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted']);
    })->middleware('auth:sanctum');
});

// GUESTS - Full CRUD
Route::prefix('admin/guests')->group(function () {
    Route::get('/', function (Request $request) {
        $query = \App\Models\Guest::with(['category', 'branch']);
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $guests = $query->orderBy('name')->get()->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'category_id' => $g->category_id,
                'category_name' => $g->category->name ?? null,
                'branch_id' => $g->branch_id,
                'branch_name' => $g->branch->branch_name ?? null,
            ];
        });
        return response()->json(['success' => true, 'data' => $guests]);
    })->middleware('auth:sanctum');
    Route::get('/{id}', function ($id) {
        $guest = \App\Models\Guest::with(['category', 'branch'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $guest]);
    })->middleware('auth:sanctum');
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $guest = \App\Models\Guest::create($data);
        return response()->json(['success' => true, 'data' => $guest, 'message' => 'Guest created']);
    })->middleware('auth:sanctum');
    Route::put('/{id}', function (Request $request, $id) {
        $guest = \App\Models\Guest::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:guest_categories,id',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $guest->update($data);
        return response()->json(['success' => true, 'data' => $guest, 'message' => 'Guest updated']);
    })->middleware('auth:sanctum');
    Route::delete('/{id}', function ($id) {
        \App\Models\Guest::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Guest deleted']);
    })->middleware('auth:sanctum');
});

// GUEST CATEGORIES
Route::prefix('admin/guest-categories')->group(function () {
    Route::get('/', function () {
        $categories = \App\Models\GuestCategory::orderBy('name')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    })->middleware('auth:sanctum');
    Route::post('/', function (Request $request) {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $cat = \App\Models\GuestCategory::create($data);
        return response()->json(['success' => true, 'data' => $cat]);
    })->middleware('auth:sanctum');
    Route::delete('/{id}', function ($id) {
        \App\Models\GuestCategory::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    })->middleware('auth:sanctum');
});

// SCHEDULES - Full CRUD
Route::prefix('admin/schedules')->group(function () {
    Route::get('/', function (Request $request) {
        $query = \App\Models\FerrySchedule::with('branch');
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $schedules = $query->orderBy('hour')->orderBy('minute')->get()->map(function ($s) {
            // Compute schedule_time from hour and minute if needed
            $hour = str_pad($s->hour ?? 0, 2, '0', STR_PAD_LEFT);
            $minute = str_pad($s->minute ?? 0, 2, '0', STR_PAD_LEFT);
            $computedTime = "{$hour}:{$minute}:00";
            return [
                'id' => $s->id,
                'schedule_time' => $computedTime,
                'hour' => $s->hour,
                'minute' => $s->minute,
                'branch_id' => $s->branch_id,
                'branch_name' => $s->branch->branch_name ?? null,
            ];
        });
        return response()->json(['success' => true, 'data' => $schedules]);
    })->middleware('auth:sanctum');
    Route::get('/{id}', function ($id) {
        $schedule = \App\Models\FerrySchedule::with('branch')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $schedule]);
    })->middleware('auth:sanctum');
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'schedule_time' => 'required|string',
            'hour' => 'required|integer|min:0|max:23',
            'minute' => 'required|integer|min:0|max:59',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $schedule = \App\Models\FerrySchedule::create($data);
        return response()->json(['success' => true, 'data' => $schedule, 'message' => 'Schedule created']);
    })->middleware('auth:sanctum');
    Route::put('/{id}', function (Request $request, $id) {
        $schedule = \App\Models\FerrySchedule::findOrFail($id);
        $data = $request->validate([
            'schedule_time' => 'required|string',
            'hour' => 'required|integer|min:0|max:23',
            'minute' => 'required|integer|min:0|max:59',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $schedule->update($data);
        return response()->json(['success' => true, 'data' => $schedule, 'message' => 'Schedule updated']);
    })->middleware('auth:sanctum');
    Route::delete('/{id}', function ($id) {
        \App\Models\FerrySchedule::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Schedule deleted']);
    })->middleware('auth:sanctum');
});

// SPECIAL CHARGES - Full CRUD
Route::prefix('admin/special-charges')->group(function () {
    Route::get('/', function (Request $request) {
        $query = \App\Models\SpecialCharge::with('branch');
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $charges = $query->orderBy('id', 'desc')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'special_charge' => $c->special_charge,
                'branch_id' => $c->branch_id,
                'branch_name' => $c->branch->branch_name ?? null,
                'created_at' => $c->created_at ? $c->created_at->format('d/m/Y') : null,
            ];
        });
        return response()->json(['success' => true, 'data' => $charges]);
    })->middleware('auth:sanctum');
    Route::post('/', function (Request $request) {
        $data = $request->validate([
            'special_charge' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $charge = \App\Models\SpecialCharge::create($data);
        return response()->json(['success' => true, 'data' => $charge, 'message' => 'Special charge created']);
    })->middleware('auth:sanctum');
    Route::put('/{id}', function (Request $request, $id) {
        $charge = \App\Models\SpecialCharge::findOrFail($id);
        $data = $request->validate([
            'special_charge' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
        ]);
        $charge->update($data);
        return response()->json(['success' => true, 'data' => $charge, 'message' => 'Charge updated']);
    })->middleware('auth:sanctum');
    Route::delete('/{id}', function ($id) {
        \App\Models\SpecialCharge::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Charge deleted']);
    })->middleware('auth:sanctum');
});

// ITEM CATEGORIES - Full CRUD
Route::prefix('admin/item-categories')->group(function () {
    Route::get('/', function () {
        $categories = \App\Models\ItemCategory::orderBy('category_name')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    })->middleware('auth:sanctum');
    Route::get('/{id}', function ($id) {
        $cat = \App\Models\ItemCategory::findOrFail($id);
        return response()->json(['success' => true, 'data' => $cat]);
    })->middleware('auth:sanctum');
    Route::post('/', function (Request $request) {
        $data = $request->validate(['category_name' => 'required|string|max:100']);
        $cat = \App\Models\ItemCategory::create($data);
        return response()->json(['success' => true, 'data' => $cat, 'message' => 'Category created']);
    })->middleware('auth:sanctum');
    Route::put('/{id}', function (Request $request, $id) {
        $cat = \App\Models\ItemCategory::findOrFail($id);
        $data = $request->validate(['category_name' => 'required|string|max:100']);
        $cat->update($data);
        return response()->json(['success' => true, 'data' => $cat, 'message' => 'Category updated']);
    })->middleware('auth:sanctum');
    Route::delete('/{id}', function ($id) {
        \App\Models\ItemCategory::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    })->middleware('auth:sanctum');
});

// ============================================
// CONFIG API ROUTES (Public - No Auth Required)
// ============================================
// Configuration endpoints for mobile apps
Route::prefix('config')->group(function () {
    Route::get('/razorpay-key', [ConfigController::class, 'getRazorpayKey']);
    Route::get('/app-config', [ConfigController::class, 'getAppConfig']);
    Route::get('/server-identity', [ConfigController::class, 'getServerIdentity']);
});

// Public routes (read-only for dropdowns)
Route::get('branches', [BranchController::class, 'getBranches']);
Route::get('ferries/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch']);
Route::get('rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']);

// ============================================
// TICKET API ROUTES (for React Admin Panel)
// ============================================

// Public read-only routes (no auth required)
Route::prefix('tickets')->group(function () {
    // List tickets with filtering
    Route::get('/', function (Request $request) {
        $query = \App\Models\Ticket::with(['branch', 'ferryBoat', 'user']);

        // Date filtering
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        // Branch filter
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        // Payment mode filter
        if ($request->has('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }
        // Ferry boat filter
        if ($request->has('ferry_boat_id')) {
            $query->where('ferry_boat_id', $request->ferry_boat_id);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'ticket_no' => $t->ticket_number ?? 'TKT-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                    'ticket_number' => $t->ticket_number ?? $t->id,
                    'branch_id' => $t->branch_id,
                    'branch_name' => $t->branch->branch_name ?? 'N/A',
                    'ferry_boat_id' => $t->ferry_boat_id,
                    'ferry_name' => $t->ferryBoat->name ?? 'N/A',
                    'ferry_type' => $t->ferryBoat->type ?? 'Regular',
                    'customer_name' => $t->customer_name ?? 'Walk-in',
                    'payment_mode' => $t->payment_mode ?? 'Cash',
                    'total_amount' => $t->total_amount ?? 0,
                    'verified_at' => $t->verified_at,
                    'date' => $t->created_at->format('Y-m-d'),
                    'time' => $t->created_at->format('H:i'),
                    'created_at' => $t->created_at->format('Y-m-d H:i'),
                    'user_name' => $t->user->name ?? 'System',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    });

    // Get single ticket
    Route::get('/{id}', function ($id) {
        $ticket = \App\Models\Ticket::with(['branch', 'ferryBoat', 'lines', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    });
});

// Protected write routes (auth required)
Route::prefix('tickets')->middleware('auth:sanctum')->group(function () {
    // Create ticket
    Route::post('/', function (Request $request) {
        $user = $request->user();

        $data = $request->validate([
            'payment_mode' => 'required|string|in:Cash,Credit,Guest Pass,GPay',
            'customer_name' => 'nullable|string|max:120',
            'customer_mobile' => 'nullable|string|max:20',
            'ferry_boat_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'ferry_time' => 'nullable|string',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_name' => 'required|string',
            'lines.*.qty' => 'required|numeric|min:1',
            'lines.*.rate' => 'required|numeric|min:0',
            'lines.*.levy' => 'required|numeric|min:0',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.vehicle_name' => 'nullable|string',
            'lines.*.vehicle_no' => 'nullable|string',
        ]);

        // Calculate total
        $total = collect($data['lines'])->sum('amount');
        if (!empty($data['discount_rs'])) {
            $total -= $data['discount_rs'];
        } elseif (!empty($data['discount_pct'])) {
            $total -= ($total * $data['discount_pct'] / 100);
        }

        // Create ticket
        $ticket = \App\Models\Ticket::create([
            'branch_id' => $data['branch_id'],
            'ferry_boat_id' => $data['ferry_boat_id'],
            'payment_mode' => $data['payment_mode'],
            'customer_name' => $data['customer_name'] ?? null,
            'customer_mobile' => $data['customer_mobile'] ?? null,
            'ferry_time' => $data['ferry_time'] ?? now(),
            'discount_pct' => $data['discount_pct'] ?? null,
            'discount_rs' => $data['discount_rs'] ?? null,
            'total_amount' => $total,
            'user_id' => $user->id,
        ]);

        // Create ticket lines
        foreach ($data['lines'] as $line) {
            $ticket->lines()->create([
                'item_name' => $line['item_name'],
                'qty' => $line['qty'],
                'rate' => $line['rate'],
                'lavy' => $line['levy'],
                'amount' => $line['amount'],
                'vehicle_name' => $line['vehicle_name'] ?? null,
                'vehicle_no' => $line['vehicle_no'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'data' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number ?? $ticket->id,
                'total_amount' => $ticket->total_amount,
            ]
        ]);
    });
});

// Public routes (no authentication required)
// Using ApiController for mobile app compatibility
Route::prefix('customer')->group(function () {
    // Registration
    Route::post('generate-otp', [ApiController::class, 'sendOtp']);
    Route::post('verify-otp', [ApiController::class, 'verifyOtpLogin']);

    // Login
    Route::post('login', [ApiController::class, 'customerlogin']);
    Route::post('google-signin', [ApiController::class, 'customergoogleSignIn']);

    // Password Reset
    Route::post('password-reset/request-otp', [ApiController::class, 'requestOTP']);
    Route::post('password-reset/verify-otp', [ApiController::class, 'verifyOTP']);
    Route::post('password-reset/reset', [ApiController::class, 'resetPassword']);
});

// Token refresh endpoint (outside auth middleware so expired tokens can still refresh)
// Accepts any bearer token that exists in DB, even if Sanctum considers it expired
Route::post('auth/refresh', function (Request $request) {
    $bearerToken = $request->bearerToken();
    if (!$bearerToken) {
        return response()->json(['success' => false, 'message' => 'No token provided'], 401);
    }

    $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);
    if (!$accessToken) {
        return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
    }

    $user = $accessToken->tokenable;
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 401);
    }

    // Revoke old token and issue new one
    $accessToken->delete();
    $newToken = $user->createToken('mobile-app')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Token refreshed',
        'data' => ['token' => $newToken],
    ]);
});

// Protected routes (require authentication with customer token)
Route::middleware('customer.api')->group(function () {

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('logout', [ApiController::class, 'customerlogout']);
        Route::get('profile', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => $request->user()
            ]);
        });
        Route::put('profile', [CustomerProfileController::class, 'updateProfile']);
        Route::post('profile/upload-picture', [CustomerProfileController::class, 'uploadProfilePicture']);

        // Change Password routes
        Route::post('change-password/request-otp', [ApiController::class, 'requestChangePasswordOTP']);
        Route::post('change-password', [ApiController::class, 'changePassword']);

        Route::get('branch', [BranchController::class, 'getBranches']);

        // Ferry and rates routes
        Route::get('ferries/branch/{id}', [FerryBoatController::class, 'getFerriesByBranch']);
        Route::get('rates/branch/{id}', [ItemRateController::class, 'getItemRatesByBranch']);
    });

    // Branch routes
    Route::get('branches/{id}/to-branches', [ApiController::class, 'getToBranches']);

    // Razorpay payment routes
    Route::post('razorpay/order', [RazorpayController::class, 'createOrder']);
    Route::post('razorpay/verify', [RazorpayController::class, 'verifyPayment']);

    // Simple signature verification (for mobile app) - Smart rate limited
    Route::middleware('throttle:payment')->post('payments/verify', [RazorpayController::class, 'verifySignature']);

    // Bookings (Rate Limited)
    Route::get('bookings/success', [ApiController::class, 'getSuccessfulBookings']);
    Route::get('bookings', [ApiController::class, 'index']);

    // Create booking: Smart rate limited (60/min for auth users, handles last-minute rush)
    Route::middleware('throttle:booking')->post('bookings', [ApiController::class, 'store']);

    Route::get('bookings/{id}', [ApiController::class, 'show']);
    Route::post('bookings/{id}/cancel', [ApiController::class, 'cancel']);
});


// ============================================
// CHECKER API ROUTES (Rate Limited for Security)
// ============================================
Route::prefix('checker')->group(function () {
    // Login: Smart rate limited (5 per minute per email+IP)
    Route::middleware('throttle:login')->post('/login', [CheckerAuthController::class, 'login']);

    // Token refresh (outside auth:sanctum so expired tokens can still refresh)
    Route::post('/refresh-token', function (Request $request) {
        $bearerToken = $request->bearerToken();
        if (!$bearerToken) {
            return response()->json(['success' => false, 'message' => 'No token provided'], 401);
        }

        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearerToken);
        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $user = $accessToken->tokenable;
        if (!$user || $user->role_id !== 5) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $accessToken->delete();
        $newToken = $user->createToken('checker-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed',
            'data' => ['token' => $newToken],
        ]);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [CheckerAuthController::class, 'logout']);
        Route::get('/profile', [CheckerAuthController::class, 'profile']);

        // Ticket verification: Smart rate limited (120/min for auth checkers)
        Route::middleware('throttle:checker-verify')->post('/verify-ticket', [CheckerAuthController::class, 'verifyTicket']);
    });
});