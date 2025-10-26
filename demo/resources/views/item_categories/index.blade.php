@extends('layouts.app')

@section('content')
<style>
    .win-card { border: 2px solid #9ec5fe; background: #f8fafc; box-shadow: 2px 2px 6px rgba(0,0,0,0.15); }
    .win-header { background: #fff; text-align: center; font-weight: bold; color: darkred; padding: 6px; border-bottom: 1px solid #ccc; }
    .win-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .win-table th { background: #f0f0f0; border: 1px solid #ccc; padding: 4px; text-align: left; }
    .win-table td { border: 1px solid #ccc; padding: 4px; }
    .win-row { background: #eaffea; }
    .win-row:hover { background: #1e3a8a; color: white; }
    .win-footer { background: darkred; color: white; padding: 6px 10px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
    .btn-add { background: #1d4ed8; color: white; padding: 4px 10px; border-radius: 3px; text-decoration: none; font-size: 13px; }
    .btn-small { font-size: 12px; padding: 4px 8px; }
    .scroll-box { max-height: 400px; overflow-y: auto; }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="win-card w-3/4">
        <!-- Header -->
        <div class="win-header">Item Categories</div>

        <!-- Filters -->
        <div class="flex gap-2 px-4 py-2">
            <form method="GET" class="flex w-full gap-2">
                <input type="text" name="id" placeholder="Category ID" value="{{ request('id') }}" class="border px-2 py-1 text-sm w-1/4">
                <input type="text" name="category_name" placeholder="Category Name" value="{{ request('category_name') }}" class="border px-2 py-1 text-sm flex-1">
                <button type="submit" class="border px-3 py-1 bg-gray-200">Search</button>
                <a href="{{ route('item_categories.index') }}" class="border px-3 py-1 bg-gray-200">Reset</a>
            </form>
        </div>

        <!-- Table -->
        <div class="scroll-box">
            <table class="win-table">
                <thead>
                    <tr>
                        <th style="width:10%">Category ID</th>
                        <th>Item Category Name</th>
                        {{-- <th style="width:15%">Levy</th> --}}
                        @if(in_array(auth()->user()->role_id, [1,2,3]))
                            <th style="width:20%">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr class="win-row">
                            <td>{{ $cat->id }}</td>
                            <td>{{ $cat->category_name }}</td>
                            {{-- <td class="text-right">{{ number_format($cat->levy, 2) }}</td> --}}
                            @if(in_array(auth()->user()->role_id, [1,2,3]))
                                <td>
                                    <a href="{{ route('item_categories.edit', $cat) }}" class="btn-small" style="background:#1d4ed8;color:#fff;border-radius:4px;padding:5px 10px;text-decoration:none;margin-right:4px;">Edit</a>
                                    <form action="{{ route('item_categories.destroy', $cat) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this category?')" class="btn-small" style="background:#b3262e;color:#fff;border-radius:4px;padding:5px 10px;border:none;">Delete</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center p-2">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="win-footer">
            <span>Total: {{ $total }}</span>
              @if(in_array(auth()->user()->role_id, [1,2,3]))
            <a href="{{ route('item_categories.create') }}" class="btn-add">Add New Item Category</a>
            @endif
        </div>
    </div>
</div>
@endsection
