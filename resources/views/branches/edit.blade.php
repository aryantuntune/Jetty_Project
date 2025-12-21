{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Edit Branch')
@section('page-title', 'Edit Branch')

@section('content')
<div class="mb-8">
    <a href="{{ route('branches.index') }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Branches</span>
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-primary-600 to-primary-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Edit Branch</h2>
                    <p class="text-sm text-primary-100">Update the details below</p>
                </div>
            </div>
        </div>

        <form action="{{ route('branches.update', $branch->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="branch_id" class="block text-sm font-medium text-slate-700 mb-2">Branch ID <span class="text-red-500">*</span></label>
                <input type="text" name="branch_id" id="branch_id" value="{{ old('branch_id', $branch->branch_id) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none @error('branch_id') border-red-500 @enderror" placeholder="Enter branch ID" required>
                @error('branch_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="branch_name" class="block text-sm font-medium text-slate-700 mb-2">Branch Name <span class="text-red-500">*</span></label>
                <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $branch->branch_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none @error('branch_name') border-red-500 @enderror" placeholder="Enter branch name" required>
                @error('branch_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                <a href="{{ route('branches.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Branch</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
