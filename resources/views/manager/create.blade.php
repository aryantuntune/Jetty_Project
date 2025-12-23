@extends('layouts.admin')

@section('title', 'Add Manager')
@section('page-title', 'Add Manager')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('manager.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Managers
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Add New Manager</h2>
        <p class="text-slate-500 mt-1">Create a new manager account with branch assignment</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('manager.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all @error('name') border-red-300 @enderror"
                        placeholder="Enter manager's full name">
                </div>
                @error('name')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="mail" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all @error('email') border-red-300 @enderror"
                        placeholder="manager@example.com">
                </div>
                @error('email')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all @error('password') border-red-300 @enderror"
                        placeholder="Create a secure password">
                </div>
                @error('password')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Mobile Field -->
            <div>
                <label for="mobile" class="block text-sm font-semibold text-slate-700 mb-2">
                    Mobile Number
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="phone" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all @error('mobile') border-red-300 @enderror"
                        placeholder="Enter mobile number">
                </div>
                @error('mobile')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Branch Field -->
            <div>
                <label for="branch_id" class="block text-sm font-semibold text-slate-700 mb-2">
                    Assigned Branch
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="map-pin" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <select name="branch_id" id="branch_id"
                        class="w-full pl-12 pr-10 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all appearance-none bg-white @error('branch_id') border-red-300 @enderror">
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400"></i>
                    </div>
                </div>
                @error('branch_id')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('manager.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Save Manager
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
