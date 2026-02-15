<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Razorpay\Api\Api;

class BookingController extends Controller
{
    public function show()
    {
        // Get all branch IDs that appear in the routes table
        $branchIds = DB::table('routes')->pluck('branch_id')->toArray();
        $branches = Branch::whereIn('id', $branchIds)
            ->select('id', 'branch_name')
            ->orderBy('branch_name')
            ->get();

        return Inertia::render('Customer/Booking', ['branches' => $branches]);
    }

    public function getToBranches($branchId)
    {
        // Get ALL route_ids that this branch belongs to (not just one!)
        $routeIds = DB::table('routes')
            ->where('branch_id', $branchId)
            ->pluck('route_id');

        if ($routeIds->isEmpty()) {
            return response()->json([]);
        }

        // Get all connected branch IDs from ALL routes this branch is on
        $toBranchIds = DB::table('routes')
            ->whereIn('route_id', $routeIds)
            ->where('branch_id', '!=', $branchId)
            ->distinct()
            ->pluck('branch_id');

        $branches = Branch::whereIn('id', $toBranchIds)
            ->select('id', 'branch_name')
            ->orderBy('branch_name')
            ->get();

        return response()->json($branches);
    }

    // public function submit(Request $request)
    // {
    //     $validated = $request->validate([
    //         'from_branch' => 'required',
    //         'to_branch' => 'required',
    //         'items' => 'required|string|max:255',
    //         'date' => 'required|date',
    //     ]);

    //     return redirect()->route('booking.form')->with('success', 'Booking submitted successfully!');
    // }

    public function getItems($branchId)
    {
        // Get items for specific branch OR global items (null branch_id)
        $items = \App\Models\ItemRate::where(function ($query) use ($branchId) {
            $query->where('branch_id', $branchId)
                ->orWhereNull('branch_id');
        })
            ->where('is_active', true)  // Boolean, not 'Y'
            ->effective()   // apply date filter
            ->select('id', 'item_name')
            ->orderBy('item_name')
            ->get();

        return response()->json($items);
    }

    public function getItemRate($itemRateId)
    {
        $item = \App\Models\ItemRate::select('item_rate', 'item_lavy')
            ->find($itemRateId);

        return response()->json($item);
    }

    public function getSchedules($branchId)
    {
        $schedules = \App\Models\FerrySchedule::where('branch_id', $branchId)
            ->where('is_active', true)
            ->select('hour', 'minute')
            ->distinct()
            ->orderBy('hour')
            ->orderBy('minute')
            ->get()
            ->map(function ($schedule) {
                return [
                    'schedule_time' => sprintf('%02d:%02d', $schedule->hour, $schedule->minute),
                ];
            });

        return response()->json($schedules);
    }

    // ----------------------------------------------------------------
    public function createOrder(Request $request)
    {
        // Check if Razorpay keys are configured
        if (empty(config('services.razorpay.key')) || empty(config('services.razorpay.secret'))) {
            return response()->json([
                'error' => 'Payment gateway not configured. Please contact support.',
                'message' => 'Missing Razorpay configuration',
            ], 500);
        }

        // ✅ Store everything in session FIRST
        session([
            'from_branch' => $request->from_branch,
            'to_branch' => $request->to_branch,
            'items' => $request->items,
            'grand_total' => $request->grand_total,
            'booking_date' => $request->date ?? now()->toDateString(),
            'departure_time' => $request->departure_time ?? now()->format('H:i:s'),
        ]);

        // Validate Departure Time if booking for today
        $bookingDate = $request->booking_date ?? now()->toDateString();
        $departureTime = $request->input('departure_time'); // Ensure this is passed from frontend

        if ($departureTime) {
            $now = \Carbon\Carbon::now('Asia/Kolkata');
            $bookingDateTime = \Carbon\Carbon::parse("$bookingDate $departureTime", 'Asia/Kolkata');

            if ($bookingDateTime->isToday() && $bookingDateTime->lessThan($now)) {
                if ($bookingDateTime->diffInMinutes($now) > 2) {
                    return response()->json([
                        'message' => 'Cannot book ticket for past ferry time.',
                    ], 422);
                }
            }
        }

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $order = $api->order->create([
                'receipt' => 'RCPT_' . time(),
                'amount' => $request->grand_total * 100,
                'currency' => 'INR',
            ]);

            return response()->json([
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'key' => config('services.razorpay.key'),
                'customer' => auth()->guard('customer')->user(),
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create payment order. Please try again.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPayment1(Request $request)
    {
        $signatureStatus = false;

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);
            $signatureStatus = true;
        } catch (\Exception $e) {
            $signatureStatus = false;
        }

        if ($signatureStatus) {


            $items = collect(session('items'))->map(function ($item) {
                return [
                    'item_name' => $item['item_name'] ?? '',
                    'quantity' => $item['quantity'] ?? '',
                    'rate' => $item['rate'] ?? '',
                    'vehicle_no' => $item['vehicle_no'] ?? null,
                ];
            });

            $ticketNo = null;

            do {
                $ticketNo = $this->generateTicketNo();
            } while (Booking::where('ticket_id', $ticketNo)->exists());

            $booking = Booking::create([
                'ticket_id' => $ticketNo, // OR proper ticket number
                'customer_id' => auth()->guard('customer')->id(),
                'ferry_id' => 1, // TEMP or actual ferry ID

                'from_branch' => session('from_branch'),
                'to_branch' => session('to_branch'),

                'booking_date' => session('booking_date'),
                'departure_time' => session('departure_time'),

                'items' => json_encode(session('items')),
                'total_amount' => session('grand_total'),

                'payment_id' => $request->razorpay_payment_id,
                'booking_source' => 'web',
                'status' => 'success',
            ]);

            $booking->load(['customer', 'fromBranch', 'toBranch']);

            //  SEND BOOKING CONFIRMATION EMAIL
            try {
                Mail::to($booking->customer->email)
                    ->send(new BookingConfirmationMail($booking));
            } catch (\Exception $e) {
                Log::error('Booking mail failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect('/booking')->with('success', 'Payment successful & booking confirmed!');
        }

        return redirect('/booking')->with('error', 'Payment Failed!');
    }

    private function generateTicketNo()
    {
        $year = Carbon::now()->format('Y');
        $date = Carbon::now()->format('md'); // MMDD
        $random = random_int(100000, 999999);

        return "{$year}{$date}{$random}";
    }

    /**
     * Show booking history for the logged-in customer
     */
    public function history()
    {
        $customer = auth()->guard('customer')->user();

        $bookings = Booking::with(['fromBranch', 'toBranch'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->through(function ($booking) {
                // Decode items JSON
                $booking->items = json_decode($booking->items, true) ?? [];
                return $booking;
            });

        return Inertia::render('Customer/History', ['bookings' => $bookings]);
    }

    public function view($ticket_id)
    {
        $booking = Booking::where('ticket_id', $ticket_id)->with(['customer', 'fromBranch', 'toBranch'])->firstOrFail();

        // Decode items
        $booking->items = json_decode($booking->items, true) ?? [];

        return Inertia::render('Customer/BookingView', ['booking' => $booking]);
    }

    public function sendTicket($bookingId, \App\Services\TicketPdfService $pdfService)
    {
        $booking = Booking::with([
            'customer',
            'fromBranch',
            'toBranch'
        ])->findOrFail($bookingId);

        $pdf = $pdfService->generate($booking);

        Mail::to($booking->customer->email)
            ->send(new \App\Mail\BookingConfirmationMail($booking)); // Replaced BookingTicketMail

        return response()->json([
            'status' => 'Ticket sent successfully'
        ]);
    }
}
