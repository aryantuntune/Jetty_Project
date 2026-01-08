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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Branch Filter -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <option value="">All Branches</option>
                        @endif
                        @foreach($branches as $b)
                        <option value="{{ $b->id }}" @selected(request('branch_id')==$b->id)>{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Category</label>
                    <select name="item_category_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $c)
                        <option value="{{ $c->id }}" @selected(request('item_category_id')==$c->id)>{{ $c->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                    <select name="is_vehicle" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Types</option>
                        <option value="0" @selected(request('is_vehicle')==='0')>Passengers</option>
                        <option value="1" @selected(request('is_vehicle')==='1')>Vehicles</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Search Item</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Item name..." class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                </div>

                <!-- Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 transition-colors text-sm font-medium">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i> Filter
                    </button>
                    <a href="{{ route('item-rates.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm">
                        <i data-lucide="x" class="w-4 h-4 inline"></i>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Item Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Rate (₹)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Levy (₹)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total (₹)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Valid From</th>
                        @if(in_array(auth()->user()->role_id, [1,2]))
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $lastBranch = null; @endphp
                    @forelse($itemRates as $r)
                    @if($lastBranch !== $r->branch_id && !request('branch_id'))
                    <tr class="bg-blue-50">
                        <td colspan="{{ in_array(auth()->user()->role_id, [1,2]) ? 9 : 8 }}" class="px-4 py-2">
                            <span class="font-bold text-blue-800 text-sm">
                                <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                                {{ strtoupper($r->branch->branch_name ?? 'Unknown') }}
                            </span>
                        </td>
                    </tr>
                    @php $lastBranch = $r->branch_id; @endphp
                    @endif
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ strtoupper($r->branch->branch_name ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ strtoupper($r->item_name) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ strtoupper($r->category->category_name ?? '-') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($r->is_vehicle)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                <i data-lucide="car" class="w-3 h-3 mr-1"></i> Vehicle
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i data-lucide="user" class="w-3 h-3 mr-1"></i> Passenger
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">{{ number_format($r->item_rate, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-slate-600">{{ number_format($r->item_lavy, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-bold text-green-600">{{ number_format($r->item_rate + $r->item_lavy, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $r->starting_date?->format('d/m/Y') }}</td>
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
                        <td colspan="{{ in_array(auth()->user()->role_id, [1,2]) ? 9 : 8 }}" class="px-4 py-12 text-center">
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
                Showing <span class="font-semibold">{{ $itemRates->firstItem() ?? 0 }}</span> - <span class="font-semibold">{{ $itemRates->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $itemRates->total() }}</span> records
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
