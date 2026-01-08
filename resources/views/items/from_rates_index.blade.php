@extends('layouts.admin')

@section('title', 'Items')
@section('page-title', 'Items')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Items (From Item Rates)</h2>
            <p class="text-slate-500 mt-1">View items derived from item rate slabs</p>
        </div>
        @if(in_array(auth()->user()->role_id, [1,2,3]))
        <a href="{{ route('item-rates.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add Item Rate Slab
        </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Search & Filter</span>
            </div>
        </div>
        <form method="GET" class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Item ID -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Item ID</label>
                    <input type="number" name="id" value="{{ request('id') }}" placeholder="e.g. 1" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Item Name -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Item Name</label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Search item name..." class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Branch -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <option value="">All Branches</option>
                        @endif
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                        Search
                    </button>
                    <a href="{{ route('items.from_rates.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $row)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                #{{ $row->id }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ strtoupper($row->item_name) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $row->category_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ strtoupper($row->branch_name ?? '—') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="package" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No items found</p>
                                <p class="text-slate-400 text-sm mt-1">Items will appear here from item rate slabs</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer with Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-slate-600">
                Total Records: <span class="font-semibold">{{ $items->total() }}</span>
            </p>
            <div class="flex items-center space-x-2">
                {{ $items->links() }}
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
