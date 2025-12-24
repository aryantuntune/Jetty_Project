@extends('layouts.admin')

@section('title', 'Employee Transfer')
@section('page-title', 'Employee Transfer')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Employee Transfer</h2>
            <p class="text-slate-500 mt-1">Transfer employees between branches</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Branch</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <i data-lucide="user" class="w-4 h-4 text-primary-600"></i>
                                </div>
                                <span class="font-medium text-slate-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700">
                                {{ strtoupper($user->branch?->branch_name ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('employees.transfer.form', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-primary-500 text-white text-sm font-medium rounded-lg hover:bg-primary-600 transition-colors">
                                    <i data-lucide="arrow-right-left" class="w-4 h-4 mr-1.5"></i>
                                    Transfer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <p class="text-slate-500 font-medium">No employees found</p>
                                <p class="text-slate-400 text-sm mt-1">Employees will appear here when available</p>
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
                Total Employees: <span class="font-semibold">{{ $users->count() }}</span>
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
