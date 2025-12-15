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

            // Payment verified, create booking
            DB::beginTransaction();

            // Calculate total amount from items
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $itemRate = \App\Models\ItemRate::find($item['item_rate_id']);
                if ($itemRate) {
                    $totalAmount += ($itemRate->item_rate + $itemRate->item_lavy) * $item['quantity'];
                }
            }

            // Generate QR code
            $qrCode = 'JETTY-' . strtoupper(uniqid());

            // Create booking record
            $booking = Booking::create([
                'customer_id' => $request->user()->id,
                'ferry_id' => $request->ferry_id,
                'from_branch' => $request->from_branch_id,
                'to_branch' => $request->to_branch_id,
                'booking_date' => $request->booking_date,
                'departure_time' => $request->departure_time,
                'items' => json_encode($request->items),
                'total_amount' => $totalAmount,
                'payment_id' => $request->razorpay_payment_id,
                'qr_code' => $qrCode,
                'status' => 'confirmed'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and booking created successfully',
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