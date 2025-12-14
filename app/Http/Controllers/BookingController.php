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
                    'customer_id' => $booking->customer_id,
                    'ferry_id' => $booking->ferry_id,
                    'from_branch_id' => $booking->from_branch,
                    'to_branch_id' => $booking->to_branch,
                    'booking_date' => $booking->booking_date,
                    'departure_time' => $booking->departure_time,
                    'total_amount' => floatval($booking->total_amount),
                    'status' => $booking->status,
                    'qr_code' => $booking->qr_code,
                    'created_at' => $booking->created_at->toIso8601String(),
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
            'ferry_id' => 'required|integer',
            'from_branch_id' => 'required|integer',
            'to_branch_id' => 'required|integer',
            'booking_date' => 'required|date',
            'departure_time' => 'required',
            'items' => 'required|array',
        ]);

        // Calculate total amount from items
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $itemRate = \App\Models\ItemRate::find($item['item_rate_id']);
            if ($itemRate) {
                $totalAmount += ($itemRate->item_rate + $itemRate->item_lavy) * $item['quantity'];
            }
        }

        // Generate QR code
        $qrCode = 'JETTY-' . strtoupper(uniqid());

        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'ferry_id' => $validated['ferry_id'],
            'from_branch' => $validated['from_branch_id'],
            'to_branch' => $validated['to_branch_id'],
            'booking_date' => $validated['booking_date'],
            'departure_time' => $validated['departure_time'],
            'items' => json_encode($validated['items']),
            'total_amount' => $totalAmount,
            'qr_code' => $qrCode,
            'status' => 'confirmed',
        ]);

        // Reload to get proper formatting
        $booking->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => [
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'ferry_id' => $booking->ferry_id,
                'from_branch_id' => $booking->from_branch,
                'to_branch_id' => $booking->to_branch,
                'booking_date' => $booking->booking_date,
                'departure_time' => $booking->departure_time,
                'total_amount' => floatval($booking->total_amount),
                'status' => $booking->status,
                'qr_code' => $booking->qr_code,
                'created_at' => $booking->created_at->toIso8601String(),
            ]
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
