@extends('layouts.admin')

@section('page-title', 'Houseboat Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Active Bookings -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm card-hover">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-500">Active Bookings</h3>
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $activeBookings }}</p>
            <div class="mt-2 text-xs text-green-600 font-medium">
                Currently confirmed
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm card-hover">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-500">Pending Requests</h3>
                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ $pendingBookings }}</p>
            <div class="mt-2 text-xs text-orange-600 font-medium">
                Needs attention
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm card-hover">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-500">Total Revenue</h3>
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                    <i data-lucide="indian-rupee" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-800">₹{{ number_format($totalRevenue) }}</p>
            <div class="mt-2 text-xs text-slate-500">
                All time earnings
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Recent Bookings</h2>
            <button class="text-sm text-primary-600 font-medium hover:text-primary-700">Export CSV</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Ref #</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Dates</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="table-row-hover transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">{{ $booking->booking_reference }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $booking->customer_name }}</div>
                                <div class="text-xs text-slate-400">{{ $booking->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $booking->room->name }} (x{{ $booking->room_count }})</td>
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <span class="font-medium">In:</span> {{ $booking->check_in->format('M d') }}<br>
                                    <span class="font-medium">Out:</span> {{ $booking->check_out->format('M d') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($booking->total_amount) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status == 'pending') bg-orange-100 text-orange-800
                                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-slate-100 text-slate-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.houseboat.bookings.status', $booking->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                        class="text-xs border-slate-200 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirm
                                        </option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancel
                                        </option>
                                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Complete
                                        </option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mb-3"></i>
                                    <p>No bookings found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $bookings->links() }}
        </div>
    </div>
@endsection