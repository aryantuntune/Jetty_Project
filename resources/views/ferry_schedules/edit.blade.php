{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Edit Ferry Schedule')
@section('page-title', 'Edit Ferry Schedule')

@section('content')
<div class="mb-8">
    <a href="{{ route('ferry_schedules.index') }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Schedules</span>
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-emerald-600 to-emerald-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Edit Ferry Schedule</h2>
                    <p class="text-sm text-emerald-100">Update the details below</p>
                </div>
            </div>
        </div>

        <form action="{{ route('ferry_schedules.update', $ferry_schedule) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="hour" class="block text-sm font-medium text-slate-700 mb-2">Hour (0-23) <span class="text-red-500">*</span></label>
                    <input type="number" name="hour" id="hour" value="{{ old('hour', $ferry_schedule->hour) }}" min="0" max="23" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none" placeholder="0-23" required>
                    @error('hour')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="minute" class="block text-sm font-medium text-slate-700 mb-2">Minute (0-59) <span class="text-red-500">*</span></label>
                    <input type="number" name="minute" id="minute" value="{{ old('minute', $ferry_schedule->minute) }}" min="0" max="59" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none" placeholder="0-59" required>
                    @error('minute')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                <a href="{{ route('ferry_schedules.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Schedule</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
