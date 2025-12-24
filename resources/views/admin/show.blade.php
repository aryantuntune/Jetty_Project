@extends('layouts.admin')

@section('title', 'Administrator Details')
@section('page-title', 'Administrator Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Administrators
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Administrator Details</h2>
        <p class="text-slate-500 mt-1">View administrator information</p>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="user-cog" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Profile Information</span>
            </div>
        </div>

        <div class="p-6">
            <!-- Profile Header -->
            <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-slate-200">
                <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                    <i data-lucide="user" class="w-8 h-8 text-primary-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ $admin->name }}</h3>
                    <p class="text-slate-500">{{ $admin->email }}</p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Name</p>
                            <p class="font-medium text-slate-800">{{ $admin->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i data-lucide="mail" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Email</p>
                            <p class="font-medium text-slate-800">{{ $admin->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <i data-lucide="phone" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Mobile</p>
                            <p class="font-medium text-slate-800">{{ $admin->mobile ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i data-lucide="building-2" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Branch</p>
                            <p class="font-medium text-slate-800">{{ $admin->branch?->branch_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 md:col-span-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                            <i data-lucide="ship" class="w-5 h-5 text-cyan-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Ferryboat</p>
                            <p class="font-medium text-slate-800">{{ $admin->ferryboat?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <a href="{{ route('admin.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Back to List
                </a>
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
