@extends('layouts.admin')

@section('title', 'Vehicle-wise Ticket Report')
@section('page-title', 'Vehicle-wise Ticket Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Vehicle-wise Tickets</h2>
            <p class="text-slate-500 mt-1">View ticket records filtered by vehicle information</p>
        </div>
        <a href="{{ route('reports.vehicle_tickets.export', request()->query()) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-lg shadow-emerald-500/30">
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
        <form method="GET" action="{{ route('reports.vehicle_tickets') }}" class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                <!-- Branch -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>
                            {{ $b->branch_name }}
                        </option>
                        @endforeach
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

                <!-- Vehicle No -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Vehicle No</label>
                    <input type="text" name="vehicle_no" placeholder="Enter vehicle no" value="{{ $vehicleNo }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Vehicle Name -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Vehicle Name</label>
                    <input type="text" name="vehicle_name" placeholder="Enter vehicle name" value="{{ $vehicleName }}" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Buttons -->
                <div class="flex items-end space-x-2 lg:col-span-2">
                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('reports.vehicle_tickets') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vehicle Name</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $t)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $t->ticket_date ? $t->ticket_date->format('d-m-Y') : $t->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ $t->ticket_no ? '#'.$t->ticket_no : '#'.$t->id }}</span>
                            @if($t->ticket_no)
                            <span class="text-xs text-slate-400 block">ID: {{ $t->id }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $t->payment_mode == 'Cash' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $t->payment_mode }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $t->ferryBoat->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            @php
                                $ft = optional($t->ferry_time);
                                echo $ft ? (method_exists($ft,'format') ? $ft->format('H:i:s') : $t->ferry_time) : '-';
                            @endphp
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $t->ferry_type ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($t->vehicle_no_join)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-700">
                                <i data-lucide="car" class="w-3 h-3 mr-1"></i>
                                {{ $t->vehicle_no_join }}
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $t->vehicle_name_join ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">{{ number_format($t->total_amount, 2) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="car" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No vehicle tickets found</p>
                                <p class="text-slate-400 text-sm mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($tickets->count() > 0)
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                        <td colspan="8" class="px-4 py-3 text-right font-semibold text-slate-700">Total (this page):</td>
                        <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">{{ number_format($pageTotalAmount, 2) }}</td>
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
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
