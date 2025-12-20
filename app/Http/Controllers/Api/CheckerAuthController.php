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
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // role_id = 5 → Admin
        $user = User::with(['branch', 'ferryboat'])
            ->where('email', $request->email)
            ->where('role_id', 5)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors'  => ['The provided credentials are incorrect.']
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
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'mobile'        => $user->mobile,
                    'branch_id'     => $user->branch_id,
                    'branch'        => $user->branch ? [
                        'branch_name' => $user->branch->branch_name
                    ] : null,
                    'ferry_boat_id' => $user->ferry_boat_id,
                    'ferryboat'     => $user->ferryboat ? [
                        'name' => $user->ferryboat->name
                    ] : null,
                    'created_at'    => $user->created_at
                ]
            ]
        ], 200);
    }


    public function logout(Request $request)
    {
        $user = $request->user();

        if (! $user) {
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

        if (! $user) {
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
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'mobile'        => $user->mobile,
                'branch_id'     => $user->branch_id,
                'branch'        => $user->branch ? [
                    'branch_name' => $user->branch->branch_name
                ] : null,
                'ferry_boat_id' => $user->ferry_boat_id,
                'ferryboat'     => $user->ferryboat ? [
                    'name' => $user->ferryboat->name
                ] : null,
                'created_at'    => $user->created_at
            ]
        ], 200);
    }



    public function verifyTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required',
        ]);

        $user = $request->user();
        $checkerBranchId = $user->branch_id;
        $ticketId = $request->ticket_id;


        $booking = \App\Models\Booking::where('ticket_id', $ticketId)
            ->where('from_branch', $checkerBranchId)
            ->first();


        $ticket = \App\Models\Ticket::find($ticketId);


        if (!$booking && !$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or not authorized for this branch',
            ], 404);
        }


        if (
            ($booking && $booking->verified_at !== null) ||
            ($ticket && $ticket->verified_at !== null)
        ) {

            $responseData = [];

            if ($booking && $booking->verified_at !== null) {
                $responseData['booking'] = [
                    'id' => $booking->id,
                    'ticket_id' => $booking->ticket_id,
                    'verified_at' => $booking->verified_at->toDateTimeString(),
                    'verified_by' => $booking->verified_by,
                ];
            }

            if ($ticket && $ticket->verified_at !== null) {
                $responseData['ticket'] = [
                    'id' => $ticket->id,
                    'verified_at' => $ticket->verified_at->toDateTimeString(),
                    'verified_by' => $ticket->verified_by,
                ];
            }

            return response()->json([
                'success' => false,
                'message' => 'Ticket already verified',
                'data' => $responseData,
            ], 409); // 409 Conflict
        }


        $now = Carbon::now();

        DB::transaction(function () use ($booking, $ticket, $user, $now) {
            if ($booking) {
                $booking->verified_at = $now;
                $booking->verified_by = $user->id;
                $booking->save();
            }

            if ($ticket) {
                $ticket->verified_at = $now;
                $ticket->verified_by = $user->id;
                $ticket->save();
            }
        });


        $responseData = [];

        if ($booking) {
            $responseData['booking'] = [
                'id' => $booking->id,
                'ticket_id' => $booking->ticket_id,
                'verified_at' => $booking->verified_at->toDateTimeString(),
                'verified_by' => $booking->verified_by,
            ];
        }

        if ($ticket) {
            $responseData['ticket'] = [
                'id' => $ticket->id,
                'verified_at' => $ticket->verified_at->toDateTimeString(),
                'verified_by' => $ticket->verified_by,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket verified successfully',
            'data' => $responseData,
        ]);
    }
}
