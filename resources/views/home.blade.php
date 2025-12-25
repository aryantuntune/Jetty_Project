@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Banner with Date Filter -->
<div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 md:p-8 mb-8 text-white shadow-lg">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-primary-100">Viewing metrics for <span class="font-semibold">{{ $periodLabel }}</span></p>
        </div>

        <!-- Date Filter Controls -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- View Mode Toggle -->
            <div class="flex bg-white/20 rounded-lg p-1">
                <a href="{{ route('home', ['view' => 'day', 'date' => $selectedDate]) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $viewMode === 'day' ? 'bg-white text-primary-700' : 'text-white hover:bg-white/10' }}">
                    Daily
                </a>
                <a href="{{ route('home', ['view' => 'month', 'month' => $selectedMonth]) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium transition-all {{ $viewMode === 'month' ? 'bg-white text-primary-700' : 'text-white hover:bg-white/10' }}">
                    Monthly
                </a>
            </div>

            <!-- Date/Month Picker -->
            @if($viewMode === 'day')
            <div class="flex items-center gap-2">
                <a href="{{ route('home', ['view' => 'day', 'date' => $date->copy()->subDay()->format('Y-m-d')]) }}"
                   class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </a>
                <input type="date" id="datePicker" value="{{ $selectedDate }}"
                       class="bg-white/20 border-0 rounded-lg px-4 py-2 text-white placeholder-white/60 focus:ring-2 focus:ring-white/50"
                       onchange="window.location.href='{{ route('home') }}?view=day&date=' + this.value">
                <a href="{{ route('home', ['view' => 'day', 'date' => $date->copy()->addDay()->format('Y-m-d')]) }}"
                   class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors {{ $date->isToday() ? 'opacity-50 pointer-events-none' : '' }}">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </a>
            </div>
            @else
            <div class="flex items-center gap-2">
                <a href="{{ route('home', ['view' => 'month', 'month' => $monthStart->copy()->subMonth()->format('Y-m')]) }}"
                   class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </a>
                <input type="month" id="monthPicker" value="{{ $selectedMonth }}"
                       class="bg-white/20 border-0 rounded-lg px-4 py-2 text-white placeholder-white/60 focus:ring-2 focus:ring-white/50"
                       onchange="window.location.href='{{ route('home') }}?view=month&month=' + this.value">
                <a href="{{ route('home', ['view' => 'month', 'month' => $monthStart->copy()->addMonth()->format('Y-m')]) }}"
                   class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors {{ $monthStart->format('Y-m') === now()->format('Y-m') ? 'opacity-50 pointer-events-none' : '' }}">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </a>
            </div>
            @endif

            <!-- Today/This Month Button -->
            @if($viewMode === 'day' && !$date->isToday())
            <a href="{{ route('home', ['view' => 'day']) }}" class="px-4 py-2 bg-white text-primary-700 rounded-lg font-medium hover:bg-primary-50 transition-colors text-center">
                Today
            </a>
            @elseif($viewMode === 'month' && $monthStart->format('Y-m') !== now()->format('Y-m'))
            <a href="{{ route('home', ['view' => 'month']) }}" class="px-4 py-2 bg-white text-primary-700 rounded-lg font-medium hover:bg-primary-50 transition-colors text-center">
                This Month
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Tickets -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-lucide="ticket" class="w-6 h-6 text-blue-600"></i>
            </div>
            @if($ticketsChange != 0)
            <span class="text-xs font-medium {{ $ticketsChange >= 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full flex items-center gap-1">
                <i data-lucide="{{ $ticketsChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                {{ abs($ticketsChange) }}%
            </span>
            @else
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-full">
                {{ $viewMode === 'day' ? 'Today' : 'This Month' }}
            </span>
            @endif
        </div>
        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($ticketsCount) }}</h3>
        <p class="text-sm text-slate-500 mt-1">Tickets Issued</p>
    </div>

    <!-- Revenue -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-lucide="indian-rupee" class="w-6 h-6 text-green-600"></i>
            </div>
            @if($revenueChange != 0)
            <span class="text-xs font-medium {{ $revenueChange >= 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-2 py-1 rounded-full flex items-center gap-1">
                <i data-lucide="{{ $revenueChange >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                {{ abs($revenueChange) }}%
            </span>
            @else
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded-full">
                {{ $viewMode === 'day' ? 'Today' : 'This Month' }}
            </span>
            @endif
        </div>
        <h3 class="text-2xl font-bold text-slate-800">Rs. {{ number_format($totalRevenue, 2) }}</h3>
        <p class="text-sm text-slate-500 mt-1">Revenue</p>
    </div>

    <!-- Active Ferries -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                <i data-lucide="ship" class="w-6 h-6 text-purple-600"></i>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Active</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($ferryBoatsCount) }}</h3>
        <p class="text-sm text-slate-500 mt-1">Ferry Boats</p>
    </div>

    <!-- Pending Verifications -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
            </div>
            <span class="text-xs font-medium {{ $pendingVerifications > 0 ? 'text-amber-600 bg-amber-100' : 'text-green-600 bg-green-100' }} px-2 py-1 rounded-full">
                {{ $pendingVerifications > 0 ? 'Pending' : 'All Done' }}
            </span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800">{{ number_format($pendingVerifications) }}</h3>
        <p class="text-sm text-slate-500 mt-1">Pending Verifications</p>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Quick Actions Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('ticket-entry.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 group-hover:bg-primary-200 flex items-center justify-center mb-3 transition-colors">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-primary-600"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">New Ticket</span>
                </a>

                <a href="{{ route('reports.tickets') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-green-100 group-hover:bg-green-200 flex items-center justify-center mb-3 transition-colors">
                        <i data-lucide="file-text" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">View Reports</span>
                </a>

                <a href="{{ route('guests.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center mb-3 transition-colors">
                        <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">Manage Guests</span>
                </a>

                <a href="{{ route('ferryboats.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center mb-3 transition-colors">
                        <i data-lucide="ship" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">Ferry Boats</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Recent Tickets</h2>
            <a href="{{ route('reports.tickets') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</a>
        </div>
        <div class="p-6">
            @if($recentTickets->count() > 0)
            <div class="space-y-3">
                @foreach($recentTickets as $ticket)
                <div class="flex items-center space-x-4 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 rounded-full {{ $ticket->verified_at ? 'bg-green-100' : 'bg-blue-100' }} flex items-center justify-center flex-shrink-0">
                        <i data-lucide="{{ $ticket->verified_at ? 'check-circle' : 'ticket' }}" class="w-5 h-5 {{ $ticket->verified_at ? 'text-green-600' : 'text-blue-600' }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">
                            Ticket #{{ $ticket->id }} - Rs. {{ number_format($ticket->total_amount, 2) }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $ticket->ferryBoat->name ?? 'N/A' }} | {{ $ticket->user->name ?? 'Unknown' }}
                        </p>
                    </div>
                    <span class="text-xs text-slate-400">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-slate-400">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p class="text-sm">No tickets found for this period</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- System Info -->
<div class="mt-8 bg-slate-100 rounded-2xl p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center">
                <i data-lucide="info" class="w-5 h-5 text-slate-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-700">System Status</p>
                <p class="text-xs text-slate-500">All systems operational</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-2 md:gap-4 text-sm text-slate-500">
            @if(Auth::user()->role_id == 3 && Auth::user()->ferryboat)
            <span class="flex items-center gap-1">
                <i data-lucide="ship" class="w-4 h-4"></i>
                Route: {{ Auth::user()->ferryboat->name ?? 'N/A' }}
            </span>
            <span class="hidden md:inline">|</span>
            @elseif(Auth::user()->role_id == 4 && Auth::user()->branch)
            <span class="flex items-center gap-1">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                Branch: {{ Auth::user()->branch->branch_name ?? 'N/A' }}
            </span>
            <span class="hidden md:inline">|</span>
            @elseif(in_array(Auth::user()->role_id, [1, 2]))
            <span class="flex items-center gap-1">
                <i data-lucide="globe" class="w-4 h-4"></i>
                All Branches
            </span>
            <span class="hidden md:inline">|</span>
            @endif
            <span class="flex items-center gap-1">
                <i data-lucide="shield" class="w-4 h-4"></i>
                Role: {{ $roleName }}
            </span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom date input styling for webkit browsers */
    input[type="date"]::-webkit-calendar-picker-indicator,
    input[type="month"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>
@endpush
