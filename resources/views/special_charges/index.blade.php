@extends('layouts.admin')

@section('title', 'Special Charges')
@section('page-title', 'Special Charges')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Special Charges</h2>
            <p class="text-slate-500 mt-1">Manage branch-specific special charges</p>
        </div>
        <a href="{{ route('special-charges.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add Special Charge
        </a>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center space-x-2">
                <i data-lucide="filter" class="w-5 h-5 text-slate-400"></i>
                <span class="font-semibold text-slate-700">Filters</span>
            </div>
        </div>
        <form method="GET" action="{{ route('special-charges.index') }}" class="p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('special-charges.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition-colors">
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Special Charge</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Created At</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($charges as $charge)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                #{{ $charge->id }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ strtoupper($charge->branch->branch_name ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-semibold text-slate-800">₹ {{ number_format($charge->special_charge, 2) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ optional($charge->created_at)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('special-charges.edit', $charge->id) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('special-charges.destroy', $charge->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this special charge?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="badge-dollar-sign" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No special charges found</p>
                                <p class="text-slate-400 text-sm mt-1">Add your first special charge</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        @if(method_exists($charges, 'total'))
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-slate-600">
                Total Records: <span class="font-semibold">{{ $charges->total() }}</span>
            </p>
            <div class="flex items-center space-x-2">
                {{ $charges->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
