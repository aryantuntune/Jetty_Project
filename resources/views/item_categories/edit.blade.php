{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Edit Item Category')
@section('page-title', 'Edit Item Category')

@section('content')
<div class="mb-8">
    <a href="{{ route('item_categories.index') }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-800 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Back to Categories</span>
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-600 to-indigo-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Edit Item Category</h2>
                    <p class="text-sm text-indigo-100">Update the details below</p>
                </div>
            </div>
        </div>

        <form action="{{ route('item_categories.update', $itemCategory) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="category_name" class="block text-sm font-medium text-slate-700 mb-2">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="category_name" id="category_name" value="{{ old('category_name', $itemCategory->category_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none @error('category_name') border-red-500 @enderror" placeholder="Enter category name" required>
                @error('category_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-slate-200">
                <a href="{{ route('item_categories.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Update Category</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
