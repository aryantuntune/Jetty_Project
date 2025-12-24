@extends('layouts.admin')

@section('title', 'Item Rate Slabs')
@section('page-title', 'Item Rate Slabs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Item Rate Slabs</h2>
            <p class="text-slate-500 mt-1">Manage pricing slabs for ferry items</p>
        </div>
        @if(in_array(auth()->user()->role_id, [1,2]))
        <a href="{{ route('item-rates.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Rate Slab
        </a>
        @endif
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Filters</span>
            </div>
        </div>
        <form method="GET" class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <option value="">All Branches</option>
                        @endif
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Starting Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Item ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Rate</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Levy</th>
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($itemRates as $r)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $r->starting_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                #{{ $r->item_id }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ strtoupper($r->item_name) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ strtoupper($r->category->category_name ?? '-') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ strtoupper($r->branch->branch_name ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">{{ number_format($r->item_rate, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-slate-600">{{ number_format($r->item_lavy, 2) }}</td>
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('item-rates.edit', $r) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('item-rates.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item rate?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ in_array(auth()->user()->role_id, [1,2]) ? 8 : 7 }}" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="tag" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No rate slabs found</p>
                                <p class="text-slate-400 text-sm mt-1">Add your first item rate slab</p>
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
                Total Records: <span class="font-semibold">{{ $itemRates->total() }}</span>
            </p>
            <div class="flex items-center space-x-2">
                {{ $itemRates->links() }}
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
