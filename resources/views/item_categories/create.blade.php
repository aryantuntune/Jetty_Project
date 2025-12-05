@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.15);
    }

    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 6px;
        border-bottom: 1px solid #ccc;
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

    .form-input {
        border: 1px solid #ccc;
        padding: 6px;
        width: 100%;
        font-size: 14px;
        border-radius: 3px;
    }

    .btn-save {
        background: #1d4ed8;
        color: white;
        padding: 6px 12px;
        border-radius: 3px;
        border: none;
        font-size: 14px;
    }

    .btn-back {
        background: gray;
        color: white;
        padding: 6px 12px;
        border-radius: 3px;
        border: none;
        font-size: 14px;
        text-decoration: none;
    }
</style>

<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="win-card w-1/2">
        <!-- Header -->
        <div class="win-header">
            Add New Item Category
        </div>

        <!-- Form -->
        <form action="{{ route('item_categories.store') }}" method="POST" class="p-4 space-y-3">
            @csrf

            <div>
                <label for="category_name" class="block font-bold text-sm">Category Name</label>
                <input type="text" name="category_name" id="category_name" class="form-input" required>
            </div>

            {{-- <div>
                <label for="levy" class="block font-bold text-sm">Levy</label>
                <input type="number" name="levy" id="levy" class="form-input" step="0.01" required>
            </div> --}}

            <!-- Footer -->
            <div class="win-footer mt-4">
                <a href="{{ route('item_categories.index') }}" class="btn-back">Back</a>
                <button type="submit" class="btn-save">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endsection