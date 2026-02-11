<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HouseboatBooking;
use App\Models\HouseboatRoom;
use Inertia\Inertia;

class HouseboatAdminController extends Controller
{
    /**
     * Display a listing of bookings.
     */
    public function index()
    {
        $bookings = HouseboatBooking::with('room')->orderBy('created_at', 'desc')->paginate(20);

        // Calculate stats
        $totalRevenue = HouseboatBooking::where('status', '!=', 'cancelled')->sum('total_amount');
        $activeBookings = HouseboatBooking::where('status', 'confirmed')->count();
        $pendingBookings = HouseboatBooking::where('status', 'pending')->count();

        return Inertia::render('Houseboat/Dashboard', [
            'bookings' => $bookings,
            'totalRevenue' => $totalRevenue,
            'activeBookings' => $activeBookings,
            'pendingBookings' => $pendingBookings,
        ]);
    }

    /**
     * Display rooms for management.
     */
    public function rooms()
    {
        $rooms = HouseboatRoom::all();
        return Inertia::render('Houseboat/Rooms', ['rooms' => $rooms]);
    }

    /**
     * Update room details (price, availability).
     */
    public function updateRoom(Request $request, $id)
    {
        $room = HouseboatRoom::findOrFail($id);

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'total_rooms' => 'required|integer|min:0',
            'name' => 'required|string|max:255',
        ]);

        $room->update($validated);

        return redirect()->back()->with('success', 'Room updated successfully.');
    }

    /**
     * Update booking status.
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $booking = HouseboatBooking::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update($validated);

        return redirect()->back()->with('success', 'Booking status updated.');
    }
}
