@extends('layouts.admin')

@section('title', 'Add Guest Category')
@section('page-title', 'Add Guest Category')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('guest_categories.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Guest Categories
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Add Guest Category</h2>
        <p class="text-slate-500 mt-1">Create a new guest category</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
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

        <form method="POST" action="{{ route('guest_categories.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="tag" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all" placeholder="Enter category name">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('guest_categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Save Category
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
