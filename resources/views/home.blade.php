{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version)
================================================================================
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - Modern TailwindCSS Admin Dashboard --}}
@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 md:p-8 mb-8 text-white shadow-lg">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-primary-100">Here's what's happening with your ferry operations today.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('ticket-entry.create') }}" class="inline-flex items-center space-x-2 bg-white text-primary-700 px-6 py-3 rounded-xl font-semibold hover:bg-primary-50 transition-colors shadow-md">
                <i data-lucide="plus" class="w-5 h-5"></i>
                <span>New Ticket</span>
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Tickets -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-lucide="ticket" class="w-6 h-6 text-blue-600"></i>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Today</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800">--</h3>
        <p class="text-sm text-slate-500 mt-1">Tickets Issued</p>
    </div>

    <!-- Today's Revenue -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                <i data-lucide="indian-rupee" class="w-6 h-6 text-green-600"></i>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Today</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800">--</h3>
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
        <h3 class="text-2xl font-bold text-slate-800">--</h3>
        <p class="text-sm text-slate-500 mt-1">Ferry Boats</p>
    </div>

    <!-- Pending Verifications -->
    <div class="card-hover bg-white rounded-2xl p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Pending</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800">--</h3>
        <p class="text-sm text-slate-500 mt-1">Verifications</p>
    </div>
</div>

<!-- Quick Actions -->
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
            <h2 class="text-lg font-semibold text-slate-800">Recent Activity</h2>
            <a href="{{ route('reports.tickets') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View All</a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-3 rounded-xl bg-slate-50">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="ticket" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800">Dashboard loaded successfully</p>
                        <p class="text-xs text-slate-500">Welcome to the Jetty Admin Panel</p>
                    </div>
                    <span class="text-xs text-slate-400">Just now</span>
                </div>

                <div class="text-center py-8 text-slate-400">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                    <p class="text-sm">Activity will appear here</p>
                </div>
            </div>
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
        <div class="mt-4 md:mt-0 flex items-center space-x-4 text-sm text-slate-500">
            <span>Branch: {{ Auth::user()->branch->branch_name ?? 'N/A' }}</span>
            <span class="hidden md:inline">|</span>
            <span>Role: {{ Auth::user()->role_id == 1 ? 'Administrator' : (Auth::user()->role_id == 2 ? 'Manager' : 'Operator') }}</span>
        </div>
    </div>
</div>
@endsection
