@extends('layouts.admin')

@section('title', 'Managers')
@section('page-title', 'Managers')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Managers</h2>
            <p class="text-slate-500 mt-1">Manage all manager accounts and their branch assignments</p>
        </div>
        <a href="{{ route('manager.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add Manager
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mobile</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Branch</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($managers as $manager)
                    <tr class="table-row-hover transition-colors cursor-pointer" onclick="if(event.target.tagName !== 'BUTTON' && !event.target.closest('button') && !event.target.closest('a') && !event.target.closest('form')) this.classList.toggle('bg-primary-50')">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($manager->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ $manager->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $manager->email }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $manager->mobile ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($manager->branch)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                {{ $manager->branch->branch_name }}
                            </span>
                            @else
                            <span class="text-slate-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('manager.edit', $manager) }}" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('manager.destroy', $manager) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this manager?')">
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No managers found</p>
                                <p class="text-slate-400 text-sm mt-1">Get started by adding your first manager</p>
                                <a href="{{ route('manager.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Add Manager
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer with Pagination -->
        @if($managers->hasPages() || $managers->total() > 0)
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-sm text-slate-600">
                Showing <span class="font-semibold">{{ $managers->firstItem() ?? 0 }}</span> to <span class="font-semibold">{{ $managers->lastItem() ?? 0 }}</span> of <span class="font-semibold">{{ $managers->total() }}</span> managers
            </p>
            <div class="flex items-center space-x-2">
                {{ $managers->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Reinitialize icons after page load
    lucide.createIcons();
</script>
@endpush
@endsection
