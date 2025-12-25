@extends('layouts.admin')

@section('title', 'Guest Categories')
@section('page-title', 'Guest Categories')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Guest Categories</h2>
            <p class="text-slate-500 mt-1">Manage guest category classifications</p>
        </div>
        @if(in_array(auth()->user()->role_id, [1,2,3]))
        <a href="{{ route('guest_categories.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add Guest Category
        </a>
        @endif
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider" style="width: 20%;">Category ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category Name</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                #{{ $cat->id }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-slate-800">{{ strtoupper($cat->name) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No guest categories found</p>
                                <p class="text-slate-400 text-sm mt-1">Add your first guest category</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            <p class="text-sm text-slate-600">
                Total Categories: <span class="font-semibold">{{ count($categories) }}</span>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
