<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;

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

    // ----------------------------------------------------------------
    public function createOrder(Request $request)
    {
        // ✅ Store everything in session FIRST
        session([
            'from_branch'   => $request->from_branch,
            'to_branch'     => $request->to_branch,
            'items'         => $request->items,
            'grand_total'   => $request->grand_total,
            'booking_date'  => now()->toDateString(), // OR pass from form
            'departure_time' => now()->format('H:i:s'), // OR actual ferry time
        ]);

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $order = $api->order->create([
            'receipt'  => 'RCPT_' . time(),
            'amount'   => $request->grand_total * 100,
            'currency' => 'INR',
        ]);

        return response()->json([
            'order_id' => $order['id'],
            'amount'   => $order['amount'],
            'key'      => env('RAZORPAY_KEY'),
            'customer' => auth()->guard('customer')->user(),
        ]);
    }

    public function verifyPayment1(Request $request)
    {
        $signatureStatus = false;

        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

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
                    'item_name'  => $item['item_name'] ?? '',
                    'quantity'   => $item['quantity'] ?? '',
                    'rate'       => $item['rate'] ?? '',
                    'vehicle_no' => $item['vehicle_no'] ?? null,
                ];
            });

            $ticketNo = null;

            do {
                $ticketNo = $this->generateTicketNo();
            } while (Booking::where('ticket_id', $ticketNo)->exists());

            $booking =    Booking::create([
                'ticket_id'       => $ticketNo, // OR proper ticket number
                'customer_id'     => auth()->guard('customer')->id(),
                'ferry_id'        => 1, // TEMP or actual ferry ID

                'from_branch'     => session('from_branch'),
                'to_branch'       => session('to_branch'),

                'booking_date'    => session('booking_date'),
                'departure_time'  => session('departure_time'),

                'items'           => json_encode(session('items')),
                'total_amount'    => session('grand_total'),

                'payment_id'      => $request->razorpay_payment_id,
                'booking_source'  => 'web',
                'status'          => 'success',
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
        $year   = Carbon::now()->format('Y');
        $date   = Carbon::now()->format('md'); // MMDD
        $random = random_int(100000, 999999);

        return "{$year}{$date}{$random}";
    }

    /**
     * Show booking history for the logged-in customer
     */
    public function history()
    {
        $customer = auth()->guard('customer')->user();

        $bookings = Booking::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                // Get branch names
                $fromBranch = Branch::find($booking->from_branch);
                $toBranch = Branch::find($booking->to_branch);

                $booking->from_branch_name = $fromBranch ? $fromBranch->branch_name : 'N/A';
                $booking->to_branch_name = $toBranch ? $toBranch->branch_name : 'N/A';
                $booking->items_decoded = json_decode($booking->items, true) ?? [];

                return $booking;
            });

        return view('customer.history', compact('bookings'));
    }

    public function view($ticket_id)
    {
        $booking = Booking::where('ticket_id', $ticket_id)->with(['customer', 'fromBranch', 'toBranch'])->firstOrFail();
        return view('tickets.view', compact('booking'));
    }
}
