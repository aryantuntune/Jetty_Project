<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Get all bookings for authenticated customer (API)
     */
    public function index(Request $request)
    {
        $customerId = $request->user()->id;

        $bookings = Booking::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'from_branch_id' => $booking->from_branch,
                    'to_branch_id' => $booking->to_branch,
                    'items' => $booking->items,
                    'total_amount' => $booking->total_amount,
                    'payment_id' => $booking->payment_id,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings
        ]);
    }

    /**
     * Show booking details by ID (API)
     */
    public function show(Request $request, $id)
    {
        $customerId = $request->user()->id;

        $booking = Booking::where('customer_id', $customerId)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking details retrieved successfully',
            'data' => $booking
        ]);
    }

    /**
     * Create new booking (API)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_branch' => 'required|integer',
            'to_branch' => 'required|integer',
            'items' => 'required',
            'total_amount' => 'required|numeric',
            'payment_id' => 'nullable|string',
        ]);

        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'from_branch' => $validated['from_branch'],
            'to_branch' => $validated['to_branch'],
            'items' => $validated['items'],
            'total_amount' => $validated['total_amount'],
            'payment_id' => $validated['payment_id'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    /**
     * Cancel booking (API)
     */
    public function cancel(Request $request, $id)
    {
        $customerId = $request->user()->id;

        $booking = Booking::where('customer_id', $customerId)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Booking already cancelled'
            ], 400);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'data' => $booking
        ]);
    }

    /**
     * Get to-branches for a from-branch (API)
     */
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
            ->select('id', 'branch_name as name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'To branches retrieved successfully',
            'data' => $branches
        ]);
    }

    /**
     * Get successful bookings (API)
     */
    public function getSuccessfulBookings(Request $request)
    {
        $customerId = $request->user()->id;

        $bookings = Booking::where('customer_id', $customerId)
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'from_branch_id' => $booking->from_branch,
                    'to_branch_id' => $booking->to_branch,
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
