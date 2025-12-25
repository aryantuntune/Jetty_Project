@extends('layouts.admin')

@section('title', 'Add Special Charge')
@section('page-title', 'Add Special Charge')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('special-charges.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Special Charges
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Add Special Charge</h2>
        <p class="text-slate-500 mt-1">Create a new branch-specific special charge</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if(session('success'))
        <div class="p-4 bg-green-50 border-b border-green-100">
            <div class="flex items-center space-x-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 bg-red-50 border-b border-red-100">
            <div class="flex items-center space-x-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

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

        <form method="POST" action="{{ route('special-charges.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Branch Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Branch <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="building-2" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <select name="branch_id" required class="w-full pl-12 pr-10 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all appearance-none bg-white">
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <!-- Special Charge -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Special Charge <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-medium">₹</span>
                    </div>
                    <input type="number" step="0.01" min="0" name="special_charge" value="{{ old('special_charge') }}" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all" placeholder="0.00">
                </div>
                <p class="mt-1 text-xs text-slate-500">Enter the special charge amount</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('special-charges.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Save Special Charge
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
