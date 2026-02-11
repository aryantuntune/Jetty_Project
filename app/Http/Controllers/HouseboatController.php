<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HouseboatRoom;
use Illuminate\Support\Str;
use App\Models\HouseboatBooking;
use Inertia\Inertia;

class HouseboatController extends Controller
{
    /**
     * Display the houseboat landing page.
     */
    public function index(Request $request)
    {
        $rooms = HouseboatRoom::all();

        // Pass search parameters to view if they exist
        $checkIn = $request->input('check_in', date('Y-m-d'));
        $checkOut = $request->input('check_out', date('Y-m-d', strtotime('+1 day')));
        $guests = $request->input('guests', 2);

        return Inertia::render('Houseboat/Booking', [
            'rooms' => $rooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
        ]);
    }

    /**
     * Show the checkout page.
     */
    public function checkout(Request $request)
    {
        $checkIn = $request->input('check_in', date('Y-m-d'));
        $checkOut = $request->input('check_out', date('Y-m-d', strtotime('+1 day')));

        // Calculate nights
        $start = \Carbon\Carbon::parse($checkIn);
        $end = \Carbon\Carbon::parse($checkOut);
        $nights = $end->diffInDays($start);
        $nights = $nights < 1 ? 1 : $nights;

        $cartData = json_decode($request->input('cart_data'), true);
        $cartItems = collect();
        $grandTotal = 0;

        if ($cartData) {
            foreach ($cartData as $roomId => $item) {
                $room = HouseboatRoom::find($roomId);
                if ($room) {
                    $qty = intval($item['qty']);
                    $pricePerNight = $room->price;
                    $totalPrice = $pricePerNight * $qty * $nights;

                    $cartItems->push((object) [
                        'room' => $room,
                        'qty' => $qty,
                        'price_per_night' => $pricePerNight,
                        'total_price' => $totalPrice
                    ]);

                    $grandTotal += $totalPrice;
                }
            }
        } elseif ($request->has('room_id')) {
            // Fallback for direct links
            $room = HouseboatRoom::findOrFail($request->room_id);
            $qty = 1;
            $totalPrice = $room->price * $nights;

            $cartItems->push((object) [
                'room' => $room,
                'qty' => 1,
                'price_per_night' => $room->price,
                'total_price' => $totalPrice
            ]);
            $grandTotal = $totalPrice;
        }

        return Inertia::render('Houseboat/Checkout', [
            'cartItems' => $cartItems,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'grandTotal' => $grandTotal,
        ]);
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_amount' => 'required|numeric',
            // Cart Data validation
            'cart_data' => 'nullable|json',
            'room_id' => 'nullable|exists:houseboat_rooms,id', // Fallback
        ]);

        $bookingRef = 'HB-' . strtoupper(Str::random(8));
        $cartData = json_decode($request->input('cart_data'), true);

        // Process function to create a booking record
        $createBooking = function ($roomId, $qty, $ref) use ($validated) {
            HouseboatBooking::create([
                'room_id' => $roomId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'guests_adults' => 1, // Defaulting as specific guest mapping per room is complex in this UI
                'total_amount' => 0, // Individual amount difficult to split perfectly with tax in this step, storing 0 or split? 
                // For now, let's store 0 for children and put the full amount in the first record? 
                // Better: We should probably store the per-room calculation if possible, or just the Grand Total in a separate Payment Record.
                // Simpler approach: Store 0 for now, or split the total proportionally. 
                // Let's just store the reference and handle payment separately.
                'status' => 'pending',
                'booking_reference' => $ref,
                'room_count' => $qty,
            ]);
        };

        if ($cartData) {
            foreach ($cartData as $item) {
                // $item structure: {'room_id': X, 'qty': Y}
                $createBooking($item['room_id'], $item['qty'], $bookingRef);
            }
        } elseif ($request->filled('room_id')) {
            // Fallback
            $createBooking($request->room_id, 1, $bookingRef);
        } else {
            return back()->with('error', 'No rooms selected.');
        }

        // Update the FIRST record to have the total amount for tracking (or all of them?)
        // Let's just update all records with the total amount for now so we don't lose it, 
        // essentially redundant but safer than 0.
        HouseboatBooking::where('booking_reference', $bookingRef)->update(['total_amount' => $validated['total_amount']]);

        return redirect()->route('houseboat.index')->with('success', 'Booking Request Sent! Ref: ' . $bookingRef);
    }
}
