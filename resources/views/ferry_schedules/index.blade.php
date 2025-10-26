@extends('layouts.app')

@section('content')
<style>
    .win-card { border: 2px solid #9ec5fe; background: #f8fafc; box-shadow: 2px 2px 6px rgba(0,0,0,0.15); }
    .win-header { background: #fff; text-align: center; font-weight: bold; color: darkred; padding: 6px; border-bottom: 1px solid #ccc; }
    .win-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .win-table th, .win-table td { border: 1px solid #ccc; padding: 4px; }
    .win-row { background: #eaffea; }
    .win-row:hover { background: #1e3a8a; color: white; }
    .win-footer { background: darkred; color: white; padding: 6px 10px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
    .btn-add { background: #1d4ed8; color: white; padding: 4px 10px; border-radius: 3px; text-decoration: none; font-size: 13px; }
    .scroll-box { max-height: 400px; overflow-y: auto; }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="win-card w-3/4">
        <!-- Header -->
        <div class="win-header">Ferry Schedule Times</div>

        <!-- Filter Dropdown -->
        <form method="GET" class="flex gap-2 px-4 py-2">
            <select name="branch_id" class="border px-2 py-1 text-sm">
                 @if(in_array($user->role_id, [1,2]))
                <option value="">-- All Branches --</option>
                @endif
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="border px-3 py-1 bg-gray-200">Filter</button>
        </form>

        <!-- Table -->
        <div class="scroll-box">
            <table class="win-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hour</th>
                        <th>Minute</th>
                        <th>Formatted Time</th>
                        <th>Branch</th>
                        @if(in_array($user->role_id, [1,2]))
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr class="win-row">
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->hour }}</td>
                            <td>{{ $schedule->minute }}</td>
                            <td>{{ $schedule->schedule_time }}</td>
                            <td>{{ $schedule->branch->branch_name ?? '-' }}</td>
                            @if(in_array($user->role_id, [1,2]))
                            <td>
                                <a href="{{ route('ferry_schedules.edit', $schedule) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('ferry_schedules.destroy', $schedule) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array($user->role_id,[1,2]) ? 6 : 5 }}" class="text-center">No schedules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="win-footer">
            <span>Total: {{ count($schedules) }}</span>
            @if(in_array($user->role_id, [1,2]))
            <a href="{{ route('ferry_schedules.create') }}" class="btn-add">Add New Ferry Time</a>
            @endif
        </div>
    </div>
</div>
@endsection
