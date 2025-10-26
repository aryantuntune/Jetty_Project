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
        <!-- Header -->
        <div class="win-header">Branches</div>

        <!-- Filters Form -->
        <form method="GET" action="{{ route('branches.index') }}" class="flex gap-2 px-4 py-2">
            <input type="text" name="branch_id" placeholder="Branch ID" value="{{ request('branch_id') }}" class="border px-2 py-1 text-sm w-1/4">
            <input type="text" name="branch_name" placeholder="Branch Name" value="{{ request('branch_name') }}" class="border px-2 py-1 text-sm flex-1">
            <button type="submit" class="border px-3 py-1 bg-gray-200">Filter</button>
            <a href="{{ route('branches.index') }}" class="border px-3 py-1 bg-gray-200">All</a>
        </form>

        <!-- Table -->
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

        <!-- Footer -->
        <div class="win-footer">
            <span>Total: {{ $total }}</span>
            @if(in_array(Auth::user()->role_id, [1,2]))
                <a href="{{ route('branches.create') }}" class="btn-add">Add New Branch</a>
            @endif
        </div>
    </div>
</div>
@endsection
