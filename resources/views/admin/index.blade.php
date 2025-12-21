{{-- OLD DESIGN COMMENTED OUT --}}

@extends('layouts.admin')

@section('title', 'Administrators')
@section('page-title', 'Administrators')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Administrators</h1>
        <p class="mt-1 text-sm text-slate-500">Manage system administrators</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.create') }}" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-medium transition-colors shadow-sm">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Add Administrator</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
            <i data-lucide="shield" class="w-5 h-5 text-red-600"></i>
        </div>
        <div>
            <h2 class="font-semibold text-slate-800">All Administrators</h2>
            <p class="text-sm text-slate-500">Total: {{ $administrators->total() }} administrators</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mobile</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($administrators as $admin)
                <tr class="table-row-hover">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $admin->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $admin->email }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $admin->mobile ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.edit', $admin) }}" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center" title="Edit"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                            <form action="{{ route('admin.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Delete this administrator?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4"><i data-lucide="shield" class="w-8 h-8 text-slate-400"></i></div>
                            <h3 class="text-lg font-medium text-slate-800 mb-1">No administrators found</h3>
                            <p class="text-sm text-slate-500">Add your first administrator to get started.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-600">Showing <span class="font-medium">{{ $administrators->count() }}</span> of <span class="font-medium">{{ $administrators->total() }}</span> administrators</p>
            {{ $administrators->links() }}
        </div>
    </div>
</div>
@endsection
