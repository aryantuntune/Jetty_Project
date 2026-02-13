<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\TicketLine;
use Illuminate\Support\Facades\DB;

class RazorpayController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orderData = [
                'receipt' => 'order_' . time(),
                'amount' => $request->amount * 100, // Amount in paisa
                'currency' => 'INR',
                'payment_capture' => 1 // Auto capture
            ];

            $razorpayOrder = $this->razorpay->order->create($orderData);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $razorpayOrder->id,
                    'amount' => $razorpayOrder->amount,
                    'currency' => $razorpayOrder->currency,
                    'key_id' => config('services.razorpay.key'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple payment signature verification (for mobile app)
     * Does NOT create booking - just verifies signature
     */
    public function verifySignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'nullable|string',
            'razorpay_signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'verified' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Build attributes for signature verification
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Add order_id if provided
            if ($request->razorpay_order_id) {
                $attributes['razorpay_order_id'] = $request->razorpay_order_id;
            }

            // Verify signature using Razorpay SDK
            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Signature is valid
            return response()->json([
                'verified' => true,
                'message' => 'Payment signature verified successfully'
            ]);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return response()->json([
                'verified' => false,
                'message' => 'Invalid payment signature',
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'verified' => false,
                'message' => 'Verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'ferry_id' => 'required|integer',
            'from_branch_id' => 'required|integer',
            'to_branch_id' => 'required|integer',
            'booking_date' => 'required|date',
            'departure_time' => 'required',
            'items' => 'required|array',
            'items.*.item_rate_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify payment signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Payment verified, create ticket
            DB::beginTransaction();

            // Calculate total amount and prepare ticket lines
            $totalAmount = 0;
            $ticketLines = [];
            foreach ($request->items as $item) {
                $itemRate = \App\Models\ItemRate::find($item['item_rate_id']);
                if ($itemRate) {
                    $qty = $item['quantity'];
                    $rate = $itemRate->item_rate;
                    $levy = $itemRate->item_lavy ?? 0;
                    $amount = ($rate + $levy) * $qty;
                    $totalAmount += $amount;

                    $ticketLines[] = [
                        'item_rate_id' => $itemRate->id,
                        'item_name' => $itemRate->item_name,
                        'quantity' => $qty,
                        'rate' => $rate,
                        'levy' => $levy,
                        'amount' => $amount,
                    ];
                }
            }

            // Generate ticket number and QR hash
            $ticketNo = 'TKT' . date('Ymd') . str_pad(Ticket::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            $qrHash = hash('sha256', $ticketNo . time() . $request->razorpay_payment_id);

            // Create ticket record (using correct Ticket model and fields)
            $ticket = Ticket::create([
                'ticket_no' => $ticketNo,
                'customer_id' => $request->user()->id,
                'customer_name' => $request->user()->first_name . ' ' . $request->user()->last_name,
                'customer_mobile' => $request->user()->mobile,
                'ferry_boat_id' => $request->ferry_id,
                'branch_id' => $request->from_branch_id,
                'dest_branch_id' => $request->to_branch_id,
                'ticket_date' => $request->booking_date,
                'ferry_time' => $request->departure_time,
                'no_of_units' => array_sum(array_column($ticketLines, 'quantity')),
                'total_amount' => $totalAmount,
                'net_amount' => $totalAmount,
                'payment_mode' => 'online',
                'payment_id' => $request->razorpay_payment_id,
                'qr_hash' => $qrHash,
                'source' => 'mobile_app',
                'status' => 'confirmed',
            ]);

            // Create ticket lines
            foreach ($ticketLines as $line) {
                TicketLine::create([
                    'ticket_id' => $ticket->id,
                    'item_rate_id' => $line['item_rate_id'],
                    'item_name' => $line['item_name'],
                    'quantity' => $line['quantity'],
                    'rate' => $line['rate'],
                    'levy' => $line['levy'],
                    'amount' => $line['amount'],
                ]);
            }

            DB::commit();

            // Load relationships for response
            $ticket->load(['branch', 'destBranch', 'ferryBoat']);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and booking created successfully',
                'data' => [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_no,
                    'customer_id' => $ticket->customer_id,
                    'ferry_id' => $ticket->ferry_boat_id,
                    'ferry_boat' => $ticket->ferryBoat?->name,
                    'from_branch_id' => $ticket->branch_id,
                    'from_branch' => $ticket->branch?->branch_name,
                    'to_branch_id' => $ticket->dest_branch_id,
                    'to_branch' => $ticket->destBranch?->branch_name,
                    'booking_date' => $ticket->ticket_date,
                    'departure_time' => $ticket->ferry_time,
                    'items' => $ticketLines,
                    'total_amount' => floatval($ticket->total_amount),
                    'status' => $ticket->status,
                    'qr_code' => $ticket->qr_hash,
                    'created_at' => $ticket->created_at->toIso8601String(),
                ]
            ]);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment signature verification failed'
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}