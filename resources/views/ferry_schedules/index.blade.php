{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Ferry Schedules')
@section('page-title', 'Ferry Schedules')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ferry Schedules</h1>
        <p class="mt-1 text-sm text-slate-500">Manage ferry departure times</p>
    </div>
    @if(in_array($user->role_id, [1,2]))
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('ferry_schedules.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Add Schedule</span>
        </a>
    </div>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h2 class="font-semibold text-slate-700 flex items-center space-x-2">
            <i data-lucide="filter" class="w-4 h-4"></i>
            <span>Filters</span>
        </h2>
    </div>
    <form method="GET" action="{{ route('ferry_schedules.index') }}" class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Branch</label>
                <select name="branch_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                    @if(in_array($user->role_id, [1,2]))
                    <option value="">All Branches</option>
                    @endif
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('ferry_schedules.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
            <i data-lucide="clock" class="w-5 h-5 text-emerald-600"></i>
        </div>
        <div>
            <h2 class="font-semibold text-slate-800">All Schedules</h2>
            <p class="text-sm text-slate-500">Total: {{ count($schedules) }} schedules</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Hour</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Minute</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Branch</th>
                    @if(in_array($user->role_id, [1,2]))
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($schedules as $schedule)
                <tr class="table-row-hover">
                    <td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">#{{ $schedule->id }}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <span class="font-semibold text-slate-800">{{ $schedule->schedule_time }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ str_pad($schedule->hour, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ str_pad($schedule->minute, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">{{ $schedule->branch->branch_name ?? '-' }}</span></td>
                    @if(in_array($user->role_id, [1,2]))
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('ferry_schedules.edit', $schedule) }}" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                            <form action="{{ route('ferry_schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Delete this schedule?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ in_array($user->role_id, [1,2]) ? 6 : 5 }}" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4"><i data-lucide="clock" class="w-8 h-8 text-slate-400"></i></div>
                            <h3 class="text-lg font-medium text-slate-800 mb-1">No schedules found</h3>
                            <p class="text-sm text-slate-500">Try adjusting your filter or add a new schedule.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        <p class="text-sm text-slate-600">Showing <span class="font-medium">{{ count($schedules) }}</span> schedules</p>
    </div>
</div>
@endsection
