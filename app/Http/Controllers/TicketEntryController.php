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
                    ->orderByRaw('CAST(hour AS UNSIGNED), CAST(minute AS UNSIGNED)')
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id'   => $row->id,
                            'time' => str_pad($row->hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($row->minute, 2, '0', STR_PAD_LEFT)
                        ];
                    });
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
        $first_row_ferry_schedule = FerrySchedule::where('branch_id', $user->branch_id)
            ->orderByRaw('CAST(hour AS UNSIGNED), CAST(minute AS UNSIGNED)')
            ->first();

        $last_row_ferry_schedule = FerrySchedule::where('branch_id', $user->branch_id)
            ->orderByRaw('CAST(hour AS UNSIGNED) DESC, CAST(minute AS UNSIGNED) DESC')
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


        $paymentModes = ['CASH MEMO', 'CREDIT MEMO', 'GUEST PASS', 'GPay'];

        // Existing "nextFerryTime" calculation for NON-admins
        $now = Carbon::now('Asia/Kolkata');
        $nowMins = ((int)$now->format('H')) * 60 + (int)$now->format('i');

        $nextRow = FerrySchedule::query()
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereRaw('(CAST(`hour` AS UNSIGNED)*60 + CAST(`minute` AS UNSIGNED)) > ?', [$nowMins])
            ->orderByRaw('(CAST(`hour` AS UNSIGNED)*60 + CAST(`minute` AS UNSIGNED)) ASC')
            ->first();

        if ($nextRow) {
            $nextFerryTime = $now->copy()
                ->setTime((int)$nextRow->hour, (int)$nextRow->minute, 0)
                ->format('Y-m-d\TH:i');
        } else {
            $firstRow = FerrySchedule::query()
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderByRaw('(CAST(`hour` AS UNSIGNED)*60 + CAST(`minute` AS UNSIGNED)) ASC')
                ->first();

            $nextFerryTime = $firstRow
                ? $now->copy()->addDay()->setTime((int)$firstRow->hour, (int)$firstRow->minute, 0)->format('Y-m-d\TH:i')
                : $now->format('Y-m-d\TH:i');
        }

        return view('tickets.create', compact(
            'branches',
            'branchId',
            'branchName',
            'ferryboatsBranch',
            'ferryBoatsPerBranch',
            'ferrySchedulesPerBranch', // 🔹 new
            'paymentModes',
            'nextFerryTime',
            'user',
            'last_row_ferry_schedule',
            'first_row_ferry_schedule',
            'hideFerryTime',
            'beforeFirstFerry'
        ));
    }


    // Optional: just a stub to receive the form
   public function store(Request $request)
{
    $now = Carbon::now('Asia/Kolkata');

    $data = $request->validate([
        'payment_mode' => 'required|string|in:Cash,Credit,Guest Pass,GPay',
        'customer_name' => 'nullable|string|max:120',
        'customer_mobile' => 'nullable|string|max:20|regex:/^\+?\d{10,15}$/',
        'ferry_boat_id' => 'required|integer',
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

    return response()->json([
        'ok' => false,
        'message' => 'Duplicate ticket prevented (already saved recently).'
    ]);
}


    // ✅ Create ticket header
    $ticket = \App\Models\Ticket::create([
        'branch_id'     => $branchId,
        'ferry_boat_id' => $data['ferry_boat_id'],
        'payment_mode'  => $data['payment_mode'],
        'customer_name'  => $data['customer_name'] ?? null,
        'customer_mobile' => $data['customer_mobile'] ?? null,
        'ferry_time'    => $data['ferry_time'] ?? $now,
        'discount_pct'  => $data['discount_pct'] ?? null,
        'discount_rs'   => $data['discount_rs'] ?? null,
        'total_amount'  => $total,
        'user_id'       => $user->id,
        'ferry_type'    => $request->ferry_type ?? 'SPECIAL',
        'guest_id'      => $guestId,
    ]);

    // Insert lines
    foreach ($data['lines'] as $ln) {
        $ln['user_id'] = $user->id;
        $ticket->lines()->create($ln);
    }

    return response()->json([
        'ok'        => true,
        'message'   => 'Ticket saved successfully.',
        'ticket_id' => $ticket->id,
        'total'     => $ticket->total_amount,
    ]);
}




    public function find(Request $request)
    {
        $data = $request->validate([
            'q'         => '',
            'branch_id' => '',
            'on'        => '',
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
            // 1) exact ID match FIRST
            $rate = (clone $base)->where('item_id', (int)$qval)->first();

            // 2) fallback: name search (if no exact id)
            if (!$rate) {
                $rate = (clone $base)
                    ->where('item_name', 'like', "%{$qval}%")
                    ->orderByRaw("CASE WHEN item_name = ? THEN 0
                                   WHEN item_name LIKE ? THEN 1
                                   ELSE 2 END", [$qval, "{$qval}%"])
                    ->orderByDesc('starting_date')
                    ->orderBy('item_id')
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
                ->orderBy('item_id')
                ->first();
        }

        if (!$rate) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'q' => 'No matching item rate found for this branch/date.'
            ]);
        }

        return response()->json([
            'item_id'               => $rate->item_id,
            'item_name'        => $rate->item_name,
            'item_category_id' => $rate->item_category_id,
            'item_category'    => $rate->category->category_name ?? null,
            'item_rate'        => (float)$rate->item_rate,
            'item_lavy'        => (float)$rate->item_lavy,
            'starting_date'    => optional($rate->starting_date)?->toDateString(),
            'branch_id'        => $rate->branch_id,
        ]);
    }


    public function print(Ticket $ticket)
{
    $ticket->load(['branch','ferryBoat','user','lines']); // ensure lines appear
    return view('tickets.print', compact('ticket'));
}


    
}