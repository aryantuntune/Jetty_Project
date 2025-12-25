@extends('layouts.admin')

@section('title', 'Transfer Employee')
@section('page-title', 'Transfer Employee')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('employees.transfer.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Employee List
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Transfer Employee</h2>
        <p class="text-slate-500 mt-1">Transfer {{ $user->name }} to a different branch</p>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Employee Details</span>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                    <i data-lucide="user" class="w-8 h-8 text-primary-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h3>
                    <p class="text-slate-500">
                        Current Branch:
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                            {{ strtoupper($user->branch?->branch_name ?? 'N/A') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="arrow-right-left" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Transfer Details</span>
            </div>
        </div>

        @if ($errors->any())
        <div class="p-4 bg-red-50 border-b border-red-100">
            <div class="flex items-start space-x-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5"></i>
                <div>
                    <p class="font-medium text-red-800">Please fix the following errors:</p>
                    <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('employees.transfer.update', $user->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- New Branch Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    New Branch <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="building-2" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <select name="to_branch_id" required class="w-full pl-12 pr-10 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all appearance-none bg-white">
                        <option value="">-- Choose Branch --</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400"></i>
                    </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Select the branch to transfer the employee to</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('employees.transfer.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 flex items-center">
                    <i data-lucide="arrow-right-left" class="w-4 h-4 mr-2"></i>
                    Transfer Employee
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
