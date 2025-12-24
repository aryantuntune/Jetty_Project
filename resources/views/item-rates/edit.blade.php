@extends('layouts.admin')

@section('title', 'Edit Item Rate Slab')
@section('page-title', 'Edit Item Rate Slab')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('item-rates.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Back to Item Rates
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Edit Item Rate Slab</h2>
        <p class="text-slate-500 mt-1">Update pricing slab details</p>
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

        <form method="POST" action="{{ route('item-rates.update', $itemRate) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

            <!-- Route Selection -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Route <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="route" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <select id="routeSelect" name="route_id" required class="w-full pl-12 pr-10 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all appearance-none bg-white">
                        <option value="">-- Select Route --</option>
                        @foreach($routes as $r)
                        <option value="{{ $r->route_id }}" data-branches="{{ $r->branch_ids }}" {{ in_array($itemRate->branch_id, explode(',', $r->branch_ids)) ? 'selected' : '' }}>{{ $r->branch_names }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <div id="branchHiddenContainer"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Item Name -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Item Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="item_name" value="{{ old('item_name', $itemRate->item_name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all" placeholder="Enter item name">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Item Category</label>
                    <select name="item_category_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all appearance-none bg-white">
                        <option value="">-- Select --</option>
                        @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('item_category_id', $itemRate->item_category_id)==$c->id)>{{ $c->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Item Rate -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Item Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-medium">₹</span>
                        </div>
                        <input type="number" step="0.01" min="0" name="item_rate" value="{{ old('item_rate', $itemRate->item_rate) }}" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    </div>
                </div>

                <!-- Item Levy -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Item Levy <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-medium">₹</span>
                        </div>
                        <input type="number" step="0.01" min="0" name="item_lavy" value="{{ old('item_lavy', $itemRate->item_lavy) }}" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    </div>
                </div>

                <!-- Starting Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Starting Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="starting_date" value="{{ old('starting_date', optional($itemRate->starting_date)->format('Y-m-d')) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    <p class="mt-1 text-xs text-slate-500">Effective from this date</p>
                </div>

                <!-- Ending Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Ending Date</label>
                    <input type="date" name="ending_date" value="{{ old('ending_date', optional($itemRate->ending_date)->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 outline-none transition-all">
                    <p class="mt-1 text-xs text-slate-500">Leave blank if still effective</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                <a href="{{ route('item-rates.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 flex items-center">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                    Update Rate Slab
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    lucide.createIcons();

    document.addEventListener("DOMContentLoaded", function () {
        let routeSelect = document.getElementById('routeSelect');

        function populateBranches() {
            let selected = routeSelect.options[routeSelect.selectedIndex];
            let branchIds = selected.getAttribute('data-branches');

            document.querySelectorAll('#branchHiddenContainer input').forEach(e => e.remove());

            if (branchIds) {
                branchIds.split(',').forEach(id => {
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'branch_id[]';
                    input.value = id;
                    document.getElementById('branchHiddenContainer').appendChild(input);
                });
            }
        }

        populateBranches();
        routeSelect.addEventListener('change', populateBranches);
    });
</script>
@endpush
@endsection
