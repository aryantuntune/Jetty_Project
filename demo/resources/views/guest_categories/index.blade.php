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
    .win-table th {
        background: #f0f0f0;
        border: 1px solid #ccc;
        padding: 4px;
        text-align: left;
    }
    .win-table td {
        border: 1px solid #ccc;
        padding: 4px;
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
    .scroll-box {
        max-height: 400px;
        overflow-y: auto;
    }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="win-card w-3/4">
        
        <!-- Header -->
        <div class="win-header">
            Guest Categories
        </div>

        <!-- Filters -->
        <div class="flex gap-2 px-4 py-2">
            <input type="text" placeholder="Category ID" class="border px-2 py-1 text-sm w-1/4">
            <input type="text" placeholder="Category Name" class="border px-2 py-1 text-sm flex-1">
            <button class="border px-3 py-1 bg-gray-200">All</button>
        </div>

        <!-- Table -->
        <div class="scroll-box">
            <table class="win-table">
                <thead>
                    <tr>
                        <th style="width:20%">Category ID</th>
                        <th>Item Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr class="win-row">
                            <td>{{ $cat->id }}</td>
                            <td>{{ strtoupper($cat->name) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="win-footer">
            <span>Total: {{ count($categories) }}</span>
               @if(in_array(auth()->user()->role_id, [1,2,3]))
       <a href="{{ route('guest_categories.create') }}" class="btn-add">Add New Guest Category</a>
                @endif
        </div>
    </div>
</div>
@endsection
