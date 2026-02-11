<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CheckerAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // role_id = 5 → Admin
        $user = User::with(['branch', 'ferryboat'])
            ->where('email', $request->email)
            ->where('role_id', 5)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['The provided credentials are incorrect.']
            ], 401);
        }

        // Create Sanctum token
        $token = $user->createToken('checker-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'branch_id' => $user->branch_id,
                    'branch' => $user->branch ? [
                        'branch_name' => $user->branch->branch_name
                    ] : null,
                    'ferry_boat_id' => $user->ferry_boat_id,
                    'ferryboat' => $user->ferryboat ? [
                        'name' => $user->ferryboat->name
                    ] : null,
                    'created_at' => $user->created_at
                ]
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Revoke current access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Load relations
        $user->load(['branch', 'ferryboat']);

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'branch_id' => $user->branch_id,
                'branch' => $user->branch ? [
                    'branch_name' => $user->branch->branch_name
                ] : null,
                'ferry_boat_id' => $user->ferry_boat_id,
                'ferryboat' => $user->ferryboat ? [
                    'name' => $user->ferryboat->name
                ] : null,
                'created_at' => $user->created_at
            ]
        ], 200);
    }



    public function verifyTicket(Request $request)
    {
        // Accept either qr_hash (new secure) or ticket_id (legacy)
        $request->validate([
            'qr_hash' => 'nullable|string|max:64',
            'ticket_id' => 'nullable',
        ]);

        // At least one identifier required
        if (!$request->qr_hash && !$request->ticket_id) {
            return response()->json([
                'success' => false,
                'message' => 'Either qr_hash or ticket_id is required',
            ], 400);
        }

        $user = $request->user();
        $checkerBranchId = $user->branch_id;

        // Find ticket - prioritize qr_hash (new secure method)
        $ticket = null;

        if ($request->qr_hash) {
            // New secure lookup by qr_hash
            $ticket = \App\Models\Ticket::with(['branch', 'destBranch', 'ferryBoat', 'lines'])
                ->where('qr_hash', $request->qr_hash)
                ->first();
        }

        if (!$ticket && $request->ticket_id) {
            // Legacy lookup by ticket_id
            $ticketId = $request->ticket_id;

            // Handle URL format (e.g., from old QR codes: /verify?code=10)
            if (is_string($ticketId) && preg_match('/code=(\d+)/', $ticketId, $matches)) {
                $ticketId = $matches[1];
            }

            $ticket = \App\Models\Ticket::with(['branch', 'destBranch', 'ferryBoat', 'lines'])
                ->find($ticketId);
        }

        // Check if ticket exists
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        // ROUTE-BASED RESTRICTION: Checker can verify tickets from their route
        // (i.e., tickets where origin OR destination is on the same route as checker's branch)

        // Get all branch IDs that are on the same route as the checker's branch
        $routeIds = \App\Models\Route::where('branch_id', $checkerBranchId)->pluck('route_id');
        $routeBranchIds = \App\Models\Route::whereIn('route_id', $routeIds)
            ->pluck('branch_id')
            ->toArray();

        // Include checker's own branch
        if (!in_array($checkerBranchId, $routeBranchIds)) {
            $routeBranchIds[] = $checkerBranchId;
        }

        // Check if ticket's origin branch is on the checker's route
        $originOnRoute = in_array($ticket->branch_id, $routeBranchIds);
        // Check if ticket's destination branch is on the checker's route
        $destOnRoute = $ticket->dest_branch_id && in_array($ticket->dest_branch_id, $routeBranchIds);

        if (!$originOnRoute && !$destOnRoute) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized: This ticket is not from your route',
                'data' => [
                    'ticket_from' => $ticket->branch?->branch_name ?? 'Unknown',
                    'ticket_to' => $ticket->dest_branch_name ?? $ticket->destBranch?->branch_name ?? 'N/A',
                    'your_branch' => $user->branch?->branch_name ?? 'Unknown',
                ]
            ], 403);
        }

        // Check if already verified
        if ($ticket->verified_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already verified',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'ticket_no' => $ticket->ticket_no,
                        'ticket_date' => $ticket->ticket_date?->format('Y-m-d'),
                        'from_branch' => $ticket->branch?->branch_name ?? 'Unknown',
                        'to_branch' => $ticket->dest_branch_name ?? $ticket->destBranch?->branch_name ?? 'N/A',
                        'ferry_boat' => $ticket->ferryBoat?->name ?? 'N/A',
                        'ferry_time' => $ticket->ferry_time?->format('H:i'),
                        'customer_name' => $ticket->customer_name ?? 'Walk-in',
                        'customer_mobile' => $ticket->customer_mobile,
                        'net_amount' => $ticket->net_amount,
                        'no_of_units' => $ticket->no_of_units,
                        'payment_mode' => $ticket->payment_mode ?? 'Cash',
                        'verified_at' => $ticket->verified_at->toDateTimeString(),
                        'verified_by' => $ticket->checker?->name ?? $ticket->checker_id,
                    ],
                ],
            ], 409);
        }

        // Verify the ticket
        $now = Carbon::now();
        $ticket->verified_at = $now;
        $ticket->checker_id = $user->id;
        $ticket->save();

        // Return success with ticket details including route info
        return response()->json([
            'success' => true,
            'message' => 'Ticket verified successfully',
            'data' => [
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_no' => $ticket->ticket_no,
                    'ticket_date' => $ticket->ticket_date?->format('Y-m-d'),
                    'from_branch' => $ticket->branch?->branch_name ?? 'Unknown',
                    'to_branch' => $ticket->dest_branch_name ?? $ticket->destBranch?->branch_name ?? 'N/A',
                    'ferry_boat' => $ticket->ferryBoat?->name ?? 'N/A',
                    'ferry_time' => $ticket->ferry_time?->format('H:i'),
                    'customer_name' => $ticket->customer_name ?? 'Walk-in',
                    'customer_mobile' => $ticket->customer_mobile,
                    'net_amount' => $ticket->net_amount,
                    'no_of_units' => $ticket->no_of_units,
                    'verified_at' => $now->toDateTimeString(),
                    'verified_by' => $user->name,
                ],
            ],
        ]);
    }

    public function scanTicket($ticket_id, Request $request)
    {
        // Create a fake request to reuse existing logic
        $request->merge([
            'ticket_id' => $ticket_id
        ]);

        return $this->verifyTicket($request);
    }
}
