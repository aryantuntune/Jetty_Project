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
                    'currency' => $razorpayOrder->currency
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'customer_id' => 'required|integer',
            'from_branch' => 'required|integer',
            'to_branch' => 'required|integer',
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0'
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

            // Payment verified, create booking
            DB::beginTransaction();

            // Create booking record
            $booking = Booking::create([
                'customer_id' => $request->customer_id,
                'from_branch_id' => $request->from_branch,
                'to_branch_id' => $request->to_branch,
                'booking_date' => now(),
                'total_amount' => $request->grand_total,
                'payment_status' => 'paid',
                'payment_id' => $request->razorpay_payment_id,
                'status' => 'confirmed'
            ]);

            // Create ticket
            $ticket = Ticket::create([
                'booking_id' => $booking->id,
                'customer_id' => $request->customer_id,
                'ticket_number' => 'TKT-' . time() . '-' . $booking->id,
                'total_amount' => $request->grand_total,
                'status' => 'active'
            ]);

            // Create ticket lines
            foreach ($request->items as $item) {
                TicketLine::create([
                    'ticket_id' => $ticket->id,
                    'item_rate_id' => $item['item_rate_id'],
                    'quantity' => $item['qty'],
                    'rate' => $item['rate'],
                    'total' => $item['qty'] * $item['rate']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and booking created successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number
                ]
            ]);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
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