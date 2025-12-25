{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'View Guest')
@section('page-title', 'View Guest')

@section('content')
<div class="mb-8">
    <a href="{{ route('guests.index') }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Guests</span>
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-orange-600 to-orange-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Guest Details</h2>
                    <p class="text-sm text-orange-100">View guest information</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-4">
            @if(isset($guest))
            <div class="flex items-center justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-3xl font-bold">
                    {{ strtoupper(substr($guest->name ?? 'G', 0, 1)) }}
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                    <span class="text-sm font-medium text-slate-500">Guest Name</span>
                    <span class="text-sm font-semibold text-slate-800">{{ $guest->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                    <span class="text-sm font-medium text-slate-500">Category</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-50 text-purple-700">{{ $guest->category->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-slate-100">
                    <span class="text-sm font-medium text-slate-500">Branch</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">{{ $guest->branch->branch_name ?? 'N/A' }}</span>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="user-x" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-1">Guest not found</h3>
                <p class="text-sm text-slate-500">The requested guest could not be found.</p>
            </div>
            @endif
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
            <a href="{{ route('guests.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium transition-colors">Back to List</a>
        </div>
    </div>
</div>
@endsection
