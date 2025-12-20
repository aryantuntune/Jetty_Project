{{--
================================================================================
OLD DESIGN - COMMENTED OUT (Bootstrap Version)
================================================================================
@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
    }
    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 6px;
        border-bottom: 1px solid #ccc;
    }
    .win-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .win-table th, .win-table td {
        border: 1px solid #ccc;
        padding: 4px;
        text-align: left;
    }
    .win-row {
        background: #eaffea;
    }
    .win-row:hover {
        background: #1e3a8a;
        color: white;
    }
    .win-footer {
        background: darkred;
        color: white;
        padding: 6px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
    }
    .btn-add {
        background: #1d4ed8;
        color: white;
        padding: 4px 10px;
        border-radius: 3px;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-action {
        padding: 2px 6px;
        font-size: 12px;
        color: #fff;
        border-radius: 3px;
        text-decoration: none;
        margin-right: 2px;
    }
    .btn-edit { background: #0b61d6; }
    .btn-delete { background: #b3262e; }
    .scroll-box { max-height: 400px; overflow-y: auto; }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container p-4">
    <div class="win-card w-1/2">
        <div class="win-header">Branches</div>
        <form method="GET" action="{{ route('branches.index') }}" class="flex gap-2 px-4 py-2">
            <input type="text" name="branch_id" placeholder="Branch ID" value="{{ request('branch_id') }}" class="border px-2 py-1 text-sm w-1/4">
            <input type="text" name="branch_name" placeholder="Branch Name" value="{{ request('branch_name') }}" class="border px-2 py-1 text-sm flex-1">
            <button type="submit" class="border px-3 py-1 bg-gray-200">Filter</button>
            <a href="{{ route('branches.index') }}" class="border px-3 py-1 bg-gray-200">All</a>
        </form>
        <div class="scroll-box">
            <table class="win-table">
                <thead>
                    <tr>
                        <th style="width:20%">Branch ID</th>
                        <th>Branch Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
               <tbody>
    @forelse($branches as $branch)
        <tr class="win-row">
            <td>{{ $branch->branch_id }}</td>
            <td>{{ $branch->branch_name }}</td>
            <td>
                @if(in_array(Auth::user()->role_id, [1,2]))
                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3" class="text-center">No branches found.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
        <div class="win-footer">
            <span>Total: {{ $total }}</span>
            @if(in_array(Auth::user()->role_id, [1,2]))
                <a href="{{ route('branches.create') }}" class="btn-add">Add New Branch</a>
            @endif
        </div>
    </div>
</div>
@endsection
================================================================================
END OF OLD DESIGN
================================================================================
--}}

{{-- NEW DESIGN - Modern TailwindCSS Admin --}}
@extends('layouts.admin')

@section('title', 'Branches')
@section('page-title', 'Branches')

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Branches</h1>
        <p class="mt-1 text-sm text-slate-500">Manage your ferry branch locations</p>
    </div>
    @if(in_array(Auth::user()->role_id, [1,2]))
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('branches.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Add Branch</span>
        </a>
    </div>
    @endif
</div>

<!-- Filters Card -->
<div class="bg-white rounded-2xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
        <h2 class="font-semibold text-slate-700 flex items-center space-x-2">
            <i data-lucide="filter" class="w-4 h-4"></i>
            <span>Filters</span>
        </h2>
    </div>
    <form method="GET" action="{{ route('branches.index') }}" class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Branch ID</label>
                <input
                    type="text"
                    name="branch_id"
                    value="{{ request('branch_id') }}"
                    placeholder="Search by ID..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Branch Name</label>
                <input
                    type="text"
                    name="branch_name"
                    value="{{ request('branch_name') }}"
                    placeholder="Search by name..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none text-sm"
                >
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-xl font-medium transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Search</span>
                </button>
                <a href="{{ route('branches.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center">
                <i data-lucide="building-2" class="w-5 h-5 text-primary-600"></i>
            </div>
            <div>
                <h2 class="font-semibold text-slate-800">All Branches</h2>
                <p class="text-sm text-slate-500">Total: {{ $total }} branches</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Branch ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Branch Name</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($branches as $branch)
                <tr class="table-row-hover">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                            {{ $branch->branch_id }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($branch->branch_name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $branch->branch_name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end space-x-2">
                            @if(in_array(Auth::user()->role_id, [1,2]))
                            <a href="{{ route('branches.edit', $branch->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this branch?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-sm text-slate-400">No actions</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                <i data-lucide="building-2" class="w-8 h-8 text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-800 mb-1">No branches found</h3>
                            <p class="text-sm text-slate-500">Try adjusting your search or add a new branch.</p>
                            @if(in_array(Auth::user()->role_id, [1,2]))
                            <a href="{{ route('branches.create') }}" class="mt-4 inline-flex items-center space-x-2 text-primary-600 hover:text-primary-700 font-medium">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Add new branch</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-600">
                Showing <span class="font-medium">{{ $branches->count() }}</span> of <span class="font-medium">{{ $total }}</span> branches
            </p>
        </div>
    </div>
</div>
@endsection
