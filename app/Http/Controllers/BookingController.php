<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;

class BookingController extends Controller
{
    public function show()
    {
        // Get all branch IDs that appear in the routes table
        $branchIds = DB::table('routes')->pluck('branch_id')->toArray();
        //dd( $branchIds);
        // Fetch branches that exist in routes
        $branches = Branch::whereIn('id', $branchIds)
            ->select('id', 'branch_name')
            ->orderBy('branch_name')
            ->get();

        // Check in debug if this returns data
        // dd($branches); // Uncomment this line temporarily if still empty
        //  dd( $branches);
        return view('customer.dashboard', compact('branches'));
    }

    public function getToBranches($branchId)
    {
        $routeId = DB::table('routes')->where('branch_id', $branchId)->value('route_id');
        if (!$routeId) {
            return response()->json([
                'success' => false,
                'message' => 'No routes found for this branch',
                'data' => []
            ]);
        }

        $toBranchIds = DB::table('routes')
            ->where('route_id', $routeId)
            ->where('branch_id', '!=', $branchId)
            ->pluck('branch_id');

        $branches = Branch::whereIn('id', $toBranchIds)
            ->select('id', 'branch_name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'To branches retrieved successfully',
            'data' => $branches
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'from_branch' => 'required',
            'to_branch' => 'required',
            'items' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        return redirect()->route('booking.form')->with('success', 'Booking submitted successfully!');
    }

    public function getItems($branchId)
    {
        $items = \App\Models\ItemRate::where('branch_id', $branchId)
            ->effective()   // apply date filter
            ->select('id', 'item_name')
            ->get();

        return response()->json($items);
    }

    public function getItemRate($itemRateId)
    {
        $item = \App\Models\ItemRate::select('item_rate', 'item_lavy')
            ->find($itemRateId);

        return response()->json($item);
    }

    public function getSuccessfulBookings()
    {
        $customerId = auth()->id();

        $bookings = \App\Models\Booking::where('customer_id', $customerId)
            ->where('status', 'confirmed')
            ->with(['fromBranch', 'toBranch', 'ticket'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'ticket_number' => $booking->ticket->ticket_number ?? null,
                    'from_branch' => $booking->fromBranch->branch_name ?? null,
                    'to_branch' => $booking->toBranch->branch_name ?? null,
                    'booking_date' => $booking->booking_date,
                    'total_amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Successful bookings retrieved successfully',
            'data' => $bookings
        ]);
    }
}