@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;   /* light blue border */
        background: #f8fafc;         /* soft background */
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
    }
    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 8px;
        border-bottom: 1px solid #ccc;
        font-size: 18px;
    }
    .form-row {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }
    .form-row label {
        font-weight: 600;
        margin-bottom: 4px;
    }
    .form-row input {
        border: 1px solid #c9c9c9;
        border-radius: 4px;
        padding: 8px;
        font-size: 14px;
    }
    .win-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: darkred;
        color: #fff;
        padding: 10px 12px;
        font-weight: bold;
        border-radius: 0 0 6px 6px;
    }
    .btn-add {
        background: #1d4ed8;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
    }
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
    }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="win-card w-1/2">
        <!-- Header -->
        <div class="win-header">
            Edit Item Category
        </div>

        <!-- Form -->
        <form action="{{ route('item_categories.update', $itemCategory) }}" method="POST" class="p-4">
            @csrf
            @method('PUT')

            <div class="form-row">
                <label>Category Name</label>
                <input type="text" name="category_name" value="{{ old('category_name', $itemCategory->category_name) }}" required>
                @error('category_name')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- <div class="form-row">
                <label>Levy</label>
                <input type="number" name="levy" value="{{ old('levy', $itemCategory->levy) }}" step="0.01" required>
                @error('levy')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div> --}}

            <!-- Footer Buttons -->
            <div class="win-footer">
                <a href="{{ route('item_categories.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-add">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection
