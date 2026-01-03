@extends('layouts.admin')

@section('title', 'Ticket Details Report')
@section('page-title', 'Ticket Details Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Ticket Details</h2>
            <p class="text-slate-500 mt-1">View and filter ticket transaction records</p>
        </div>
        <a href="{{ route('reports.tickets.export', request()->query()) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-500/30">
            <i data-lucide="download" class="w-5 h-5 mr-2"></i>
            Export CSV
        </a>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Filters</span>
            </div>
        </div>
        <form method="GET" action="{{ route('reports.tickets') }}" class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                <!-- Branch -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" id="branch_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Mode -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Payment Mode</label>
                    <select name="payment_mode" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Modes</option>
                        @foreach($paymentModes as $mode)
                        <option value="{{ $mode }}" {{ $paymentMode == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ferry Type -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Ferry Type</label>
                    <select name="ferry_type" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Types</option>
                        @foreach($ferryTypes as $type)
                        <option value="{{ $type }}" {{ $ferryType == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ferry Boat -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Ferry Boat</label>
                    <select name="ferry_boat_id" id="ferry_boat_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Boats</option>
                        @if($branchId)
                        @foreach($ferryBoats->where('branch_id', $branchId) as $boat)
                        <option value="{{ $boat->id }}" {{ $ferryBoatId == $boat->id ? 'selected' : '' }}>
                            {{ $boat->name }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('reports.tickets') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ticket #</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pay Mode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Boat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Print</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $ticket->ticket_date ? $ticket->ticket_date->format('d-m-Y') : $ticket->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ $ticket->ticket_no ? '#'.$ticket->ticket_no : '#'.$ticket->id }}</span>
                            @if($ticket->ticket_no)
                            <span class="text-xs text-slate-400 block">ID: {{ $ticket->id }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->payment_mode == 'Cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $ticket->payment_mode }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $ticket->ferryBoat->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ optional($ticket->ferry_time)->format('H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $ticket->ferry_type }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $ticket->customer_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">{{ number_format($ticket->total_amount, 2) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-1">
                                <a target="_blank" rel="noopener" href="{{ route('tickets.print', ['ticket' => $ticket->id, 'w' => '58']) }}" class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition-colors">
                                    58mm
                                </a>
                                <a target="_blank" rel="noopener" href="{{ route('tickets.print', ['ticket' => $ticket->id, 'w' => '80']) }}" class="px-2 py-1 text-xs bg-slate-100 text-slate-700 rounded hover:bg-slate-200 transition-colors">
                                    80mm
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="ticket" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No tickets found</p>
                                <p class="text-slate-400 text-sm mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($tickets->count() > 0)
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                        <td colspan="7" class="px-4 py-3 text-right font-semibold text-slate-700">Total:</td>
                        <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">{{ number_format($totalAmount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Footer with Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-slate-600">
                Page {{ $tickets->currentPage() }} | Showing {{ $tickets->count() }} records
            </p>
            <div class="flex items-center space-x-2">
                @if($tickets->onFirstPage())
                    <span class="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed">« Previous</span>
                @else
                    <a href="{{ $tickets->appends(request()->query())->previousPageUrl() }}" class="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">« Previous</a>
                @endif

                <span class="px-4 py-2 text-sm text-slate-600 bg-white border border-slate-200 rounded-lg">
                    Page {{ $tickets->currentPage() }}
                </span>

                @if($tickets->hasMorePages())
                    <a href="{{ $tickets->appends(request()->query())->nextPageUrl() }}" class="px-4 py-2 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">Next »</a>
                @else
                    <span class="px-4 py-2 text-sm text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed">Next »</span>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    lucide.createIcons();

    $('#branch_id').on('change', function () {
        var branchId = $(this).val();
        var ferryBoatDropdown = $('#ferry_boat_id');

        ferryBoatDropdown.empty().append('<option value="">All Boats</option>');

        if (branchId) {
            $.ajax({
                url: "{{ url('/branches') }}/" + branchId + "/ferryboats",
                type: "GET",
                success: function (data) {
                    $.each(data, function (key, boat) {
                        ferryBoatDropdown.append(
                            '<option value="'+ boat.id +'">'+ boat.name +'</option>'
                        );
                    });
                }
            });
        }
    });
</script>
@endpush
@endsection
