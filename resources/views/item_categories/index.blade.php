{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Item Categories')
@section('page-title', 'Item Categories')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Item Categories</h1>
        <p class="mt-1 text-sm text-slate-500">Manage ticket item categories</p>
    </div>
    @if(in_array(auth()->user()->role_id, [1,2,3]))
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('item_categories.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Add Category</span>
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
    <form method="GET" action="{{ route('item_categories.index') }}" class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Category ID</label>
                <input type="text" name="id" value="{{ request('id') }}" placeholder="Search by ID..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Category Name</label>
                <input type="text" name="category_name" value="{{ request('category_name') }}" placeholder="Search by name..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search</span>
                </button>
                <a href="{{ route('item_categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
            <i data-lucide="tags" class="w-5 h-5 text-indigo-600"></i>
        </div>
        <div>
            <h2 class="font-semibold text-slate-800">All Categories</h2>
            <p class="text-sm text-slate-500">Total: {{ $total }} categories</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Category Name</th>
                    @if(in_array(auth()->user()->role_id, [1,2,3]))
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $cat)
                <tr class="table-row-hover">
                    <td class="px-6 py-4"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">#{{ $cat->id }}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white">
                                <i data-lucide="tag" class="w-4 h-4"></i>
                            </div>
                            <span class="font-medium text-slate-800">{{ $cat->category_name }}</span>
                        </div>
                    </td>
                    @if(in_array(auth()->user()->role_id, [1,2,3]))
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('item_categories.edit', $cat) }}" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                            <form action="{{ route('item_categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ in_array(auth()->user()->role_id, [1,2,3]) ? 3 : 2 }}" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4"><i data-lucide="tags" class="w-8 h-8 text-slate-400"></i></div>
                            <h3 class="text-lg font-medium text-slate-800 mb-1">No categories found</h3>
                            <p class="text-sm text-slate-500">Try adjusting your search or add a new category.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        <p class="text-sm text-slate-600">Showing <span class="font-medium">{{ count($categories) }}</span> of <span class="font-medium">{{ $total }}</span> categories</p>
    </div>
</div>
@endsection
