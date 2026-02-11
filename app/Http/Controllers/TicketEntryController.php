<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FerryBoat;
use App\Models\FerrySchedule;
use App\Models\ItemRate;
use App\Models\SpecialCharge;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TicketEntryController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role_id, [1, 2])) {
            // --- Admin/Manager ---
            $branches = Branch::orderBy('branch_name')->get(['id', 'branch_name']);
            $branchId = $branches->first()->id ?? null;
            $branchName = $branches->first()->branch_name ?? '';
            // dd($branchId);
            // Boats per branch (unique names)
            $ferryBoatsPerBranch = [];
            foreach ($branches as $b) {
                $ferryBoatsPerBranch[$b->id] = $b->ferryboats()
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->unique('name')
                    ->values();
            }

            $ferryboatsBranch = $ferryBoatsPerBranch[$branchId] ?? collect();

            // dd($ferryboatsBranch);
            // Schedules per branch
            $ferrySchedulesPerBranch = [];
            foreach ($branches as $b) {
                $ferrySchedulesPerBranch[$b->id] = FerrySchedule::where('branch_id', $b->id)
                    ->orderByRaw('CAST(hour AS INTEGER), CAST(minute AS INTEGER)')
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'time' => str_pad($row->hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($row->minute, 2, '0', STR_PAD_LEFT)
                        ];
                    })
                    ->unique('time')
                    ->values();
            }
        } else {
            // --- Normal user: only their branch ---
            $branches = Branch::where('id', $user->branch_id)->get(['id', 'branch_name']);
            $branchId = $user->branch_id;
            $branchName = optional($user->branch)->branch_name ?? '';

            $ferryboatsBranch = optional($user->branch)->ferryboats()->orderBy('name')->get(['id', 'name']);
            $ferryBoatsPerBranch = null;
            $ferrySchedulesPerBranch = null;
        }





        // Fetch first and last ferry schedules for this branch
        // Use $branchId (already set above) instead of $user->branch_id for admins
        $first_row_ferry_schedule = FerrySchedule::where('branch_id', $branchId)
            ->orderByRaw('CAST(hour AS INTEGER), CAST(minute AS INTEGER)')
            ->first();

        $last_row_ferry_schedule = FerrySchedule::where('branch_id', $branchId)
            ->orderByRaw('CAST(hour AS INTEGER) DESC, CAST(minute AS INTEGER) DESC')
            ->first();
        if ($first_row_ferry_schedule && $last_row_ferry_schedule) {
            $now = Carbon::now('Asia/Kolkata');

            // Last ferry schedule datetime today
            $lastSchedule = Carbon::today('Asia/Kolkata')
                ->setHour($last_row_ferry_schedule->hour)
                ->setMinute($last_row_ferry_schedule->minute)
                ->setSecond(0);

            // First ferry schedule datetime today
            $firstSchedule = Carbon::today('Asia/Kolkata')
                ->setHour($first_row_ferry_schedule->hour)
                ->setMinute($first_row_ferry_schedule->minute)
                ->setSecond(0);

            // Hide Ferry Time if current time is **after last ferry schedule**
            $hideFerryTime = $now->greaterThan($lastSchedule);

            // dd($lastSchedule);

            $oneHourBeforeFirst = $firstSchedule->copy()->subHour();
            $beforeFirstFerry = $now->greaterThanOrEqualTo($oneHourBeforeFirst)
                && $now->lessThan($firstSchedule);
        } else {
            // No schedules available
            $hideFerryTime = true;
            $beforeFirstFerry = false;
            $firstSchedule = null;
            $lastSchedule = null;
        }
        //  dd($beforeFirstFerry);


        $paymentModes = ['Cash', 'UPI', 'Guest Pass'];

        // Existing "nextFerryTime" calculation for NON-admins
        $now = Carbon::now('Asia/Kolkata');
        $nowMins = ((int) $now->format('H')) * 60 + (int) $now->format('i');

        $nextRow = FerrySchedule::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereRaw('(CAST(hour AS INTEGER)*60 + CAST(minute AS INTEGER)) > ?', [$nowMins])
            ->orderByRaw('(CAST(hour AS INTEGER)*60 + CAST(minute AS INTEGER)) ASC')
            ->first();

        if ($nextRow) {
            $nextFerryTime = $now->copy()
                ->setTime((int) $nextRow->hour, (int) $nextRow->minute, 0)
                ->format('Y-m-d\TH:i');
        } else {
            $firstRow = FerrySchedule::query()
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderByRaw('(CAST(hour AS INTEGER)*60 + CAST(minute AS INTEGER)) ASC')
                ->first();

            $nextFerryTime = $firstRow
                ? $now->copy()->addDay()->setTime((int) $firstRow->hour, (int) $firstRow->minute, 0)->format('Y-m-d\TH:i')
                : $now->format('Y-m-d\TH:i');
        }

        // Build destination branches per branch (for To Branch dropdown)
        $destBranchesPerBranch = [];
        foreach ($branches as $b) {
            $destBranchesPerBranch[$b->id] = \App\Models\Route::getDestinationsForBranch($b->id);
        }

        // Fetch active guests for Guest Pass dropdown
        $guests = \App\Models\Guest::active()
            ->with('category:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'category_id']);

        return Inertia::render('TicketEntry/Create', [
            'branches' => $branches,
            'branchId' => $branchId,
            'branchName' => $branchName,
            'ferryboatsBranch' => $ferryboatsBranch,
            'ferryBoatsPerBranch' => $ferryBoatsPerBranch,
            'ferrySchedulesPerBranch' => $ferrySchedulesPerBranch,
            'destBranchesPerBranch' => $destBranchesPerBranch,
            'paymentModes' => $paymentModes,
            'guests' => $guests,
            'nextFerryTime' => $nextFerryTime,
            'user' => $user,
            'last_row_ferry_schedule' => $last_row_ferry_schedule,
            'first_row_ferry_schedule' => $first_row_ferry_schedule,
            'hideFerryTime' => $hideFerryTime,
            'beforeFirstFerry' => $beforeFirstFerry,
        ]);
    }


    // Optional: just a stub to receive the form
    public function store(Request $request)
    {
        $now = Carbon::now('Asia/Kolkata');

        $data = $request->validate([
            'payment_mode' => 'required|string|in:Cash,UPI,Guest Pass',
            'guest_id' => 'nullable|integer|exists:guests,id',
            'customer_name' => 'nullable|string|max:120',
            'customer_mobile' => 'nullable|string|max:20|regex:/^\+?\d{10,15}$/',
            'ferry_boat_id' => 'required|integer',
            'dest_branch_id' => 'nullable|integer|exists:branches,id',
            'ferry_time' => '',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'nullable|string',
            'lines.*.item_name' => 'required|string',
            'lines.*.qty' => 'required|numeric|min:0',
            'lines.*.rate' => 'required|numeric|min:0',
            'lines.*.levy' => 'required|numeric|min:0',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.vehicle_name' => 'nullable|string',
            'lines.*.vehicle_no' => 'nullable|string',
            'ferry_type' => ''
        ]);

        // Validate Ferry Time (Must be future if today)
        if (!empty($data['ferry_time'])) {
            try {
                $ferryTime = \Carbon\Carbon::parse($data['ferry_time']);
                // Assuming ferry_time is today's time. 
                // If ferry_time includes date component, use isPast().
                // If it's just time, we assume it's for today as per context.
                // The input format is typically 'Y-m-d H:i:s' or similar from the frontend or just H:i.

                // Let's safe guard: strictly parsing as today's time
                $ferryDateTime = \Carbon\Carbon::today('Asia/Kolkata')->setTimeFrom($ferryTime);

                if ($ferryDateTime->isToday() && $ferryDateTime->lessThan($now)) {
                    // Allow a small grace period (e.g., 2 mins) for slow clocks/network
                    if ($ferryDateTime->diffInMinutes($now) > 2) {
                        return response()->json([
                            'message' => 'Cannot book ticket for past ferry time.',
                            'errors' => ['ferry_time' => ['Cannot book ticket for past ferry time.']]
                        ], 422);
                    }
                }
            } catch (\Exception $e) {
                // ignore parse errors here, strictly validated elsewhere or let slide
            }
        }

        $user = $request->user();
        $branchId = in_array($user->role_id, [1, 2])
            ? $request->input('branch_id')
            : $user->branch_id;

        // Calculate total
        $total = collect($data['lines'])->sum('amount');
        if (!empty($data['discount_rs'])) {
            $total -= $data['discount_rs'];
        } elseif (!empty($data['discount_pct'])) {
            $total -= ($total * $data['discount_pct'] / 100);
        }

        // Apply Special Charge (if applicable)
        // if (($request->ferry_type ?? '') === 'SPECIAL') {
        //     $specialChargeRecord = SpecialCharge::where('branch_id', $branchId)->first();
        //     $specialCharge = $specialChargeRecord ? $specialChargeRecord->special_charge : 0;

        //     $numLines = count($data['lines']);
        //     if ($numLines > 0) {
        //         $perLineCharge = round($specialCharge / $numLines, 2);
        //         $remaining = $specialCharge - ($perLineCharge * $numLines);

        //         foreach ($data['lines'] as $index => &$ln) {
        //             $ln['amount'] += $perLineCharge;
        //             if ($index === 0 && $remaining !== 0) {
        //                 $ln['amount'] += $remaining;
        //             }
        //         }
        //         unset($ln);
        //         $total = collect($data['lines'])->sum('amount');
        //     }
        // }

        $guestId = $request->guest_id;

        // ✅ Prevent accidental duplicate within 10 seconds (same user + boat + total)
        $duplicate = \App\Models\Ticket::where('branch_id', $branchId)
            ->where('ferry_boat_id', $data['ferry_boat_id'])
            ->where('user_id', $user->id)
            ->where('total_amount', $total)
            ->whereBetween('created_at', [now()->subSeconds(10), now()->addSeconds(10)])
            ->exists();

        if ($duplicate) {
            if ($request->input('guest_id') != null) {
                return redirect()->route('ticket-entry.create')
                    ->with('error', 'Duplicate ticket prevented.');
            }

            return redirect()->route('ticket-entry.create')
                ->with('error', 'Duplicate ticket prevented (already saved recently).');
        }


        // Get destination branch name if provided
        $destBranchName = null;
        if (!empty($data['dest_branch_id'])) {
            $destBranch = \App\Models\Branch::find($data['dest_branch_id']);
            $destBranchName = $destBranch?->branch_name;
        }

        // ✅ Create ticket header
        $ticket = \App\Models\Ticket::create([
            'ticket_date' => $request->input('ticket_date') ?? $now->toDateString(),
            'branch_id' => $branchId,
            'dest_branch_id' => $data['dest_branch_id'] ?? null,
            'dest_branch_name' => $destBranchName,
            'ferry_boat_id' => $data['ferry_boat_id'],
            'payment_mode' => $data['payment_mode'],
            'customer_name' => $data['customer_name'] ?? null,
            'customer_mobile' => $data['customer_mobile'] ?? null,
            'ferry_time' => $data['ferry_time'] ?? $now,
            'discount_pct' => $data['discount_pct'] ?? null,
            'discount_rs' => $data['discount_rs'] ?? null,
            'total_amount' => $total,
            'user_id' => $user->id,
            'ferry_type' => $request->ferry_type ?? 'SPECIAL',
            'guest_id' => $guestId,
        ]);

        // Insert lines
        foreach ($data['lines'] as $ln) {
            $ln['user_id'] = $user->id;
            $ticket->lines()->create($ln);
        }

        // Build secure print URL using qr_hash
        $printUrl = $ticket->qr_hash
            ? route('tickets.print.secure', ['hash' => $ticket->qr_hash])
            : route('tickets.print', ['ticket' => $ticket->id]);

        // Return redirect with flash message and ticket info for printing
        return redirect()->route('ticket-entry.create')
            ->with('success', 'Ticket created successfully!')
            ->with('ticket', [
                'id' => $ticket->id,
                'ticket_no' => $ticket->ticket_no,
                'qr_hash' => $ticket->qr_hash,
                'print_url' => $printUrl,
                'total' => $ticket->total_amount,
            ]);
    }




    public function find(Request $request)
    {
        $data = $request->validate([
            'q' => '',
            'branch_id' => '',
            'on' => '',
        ]);

        $on = $data['on'] ?? now()->toDateString();

        $base = ItemRate::query()
            ->effective($on)
            ->when(
                $data['branch_id'] ?? null,
                fn($q) =>
                $q->where('branch_id', $data['branch_id'])
            )
            ->with('category:id,category_name');

        $qval = trim($data['q']);
        $rate = null;

        if (ctype_digit($qval)) {
            // 1) exact ID match FIRST (using primary key 'id')
            $rate = (clone $base)->where('id', (int) $qval)->first();

            // 2) fallback: name search (if no exact id)
            if (!$rate) {
                $rate = (clone $base)
                    ->where('item_name', 'like', "%{$qval}%")
                    ->orderByRaw("CASE WHEN item_name = ? THEN 0
                                   WHEN item_name LIKE ? THEN 1
                                   ELSE 2 END", [$qval, "{$qval}%"])
                    ->orderByDesc('starting_date')
                    ->orderBy('id')
                    ->first();
            }
        } else {
            // Text input: prefer exact, then prefix, then contains
            $rate = (clone $base)
                ->where('item_name', 'like', "%{$qval}%")
                ->orderByRaw("CASE WHEN item_name = ? THEN 0
                               WHEN item_name LIKE ? THEN 1
                               ELSE 2 END", [$qval, "{$qval}%"])
                ->orderByDesc('starting_date')
                ->orderBy('id')
                ->first();
        }

        if (!$rate) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'q' => 'No matching item rate found for this branch/date.'
            ]);
        }

        return response()->json([
            'id' => $rate->id,
            'item_name' => $rate->item_name,
            'item_category_id' => $rate->item_category_id,
            'item_category' => $rate->category->category_name ?? null,
            'item_rate' => (float) $rate->item_rate,
            'item_lavy' => (float) $rate->item_lavy,
            'is_vehicle' => (bool) $rate->is_vehicle,
            'starting_date' => optional($rate->starting_date)?->toDateString(),
            'branch_id' => $rate->branch_id,
        ]);
    }

    /**
     * Get all items for a branch (for dropdown)
     * Items with NULL branch_id are global and available at all branches
     */
    public function listItems(Request $request)
    {
        $branchId = $request->input('branch_id');
        $on = $request->input('on', now()->toDateString());

        $items = ItemRate::query()
            ->effective($on)
            ->where(function ($q) use ($branchId) {
                // Include items for this specific branch OR global items (branch_id = NULL)
                $q->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            })
            ->orderBy('is_vehicle')
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'item_rate', 'item_lavy', 'is_vehicle', 'item_category_id', 'branch_id']);

        return response()->json($items);
    }


    public function print(Ticket $ticket)
    {
        $ticket->load(['branch', 'destBranch', 'ferryBoat', 'user', 'lines']);
        return Inertia::render('TicketEntry/Print', ['ticket' => $ticket]);
    }

    /**
     * Print ticket by secure qr_hash (no ticket ID exposure)
     */
    public function printByHash(string $hash)
    {
        $ticket = Ticket::where('qr_hash', $hash)
            ->with(['branch', 'destBranch', 'ferryBoat', 'user', 'lines'])
            ->firstOrFail();

        return Inertia::render('TicketEntry/Print', ['ticket' => $ticket]);
    }
}