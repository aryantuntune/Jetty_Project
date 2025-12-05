@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-200 container mx-auto p-4">
    <div class="card guest-card w-3/4">
        <div class="guest-header">Add New Guest</div>
        <form action="{{ route('guests.store') }}" method="POST" class="guest-form">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Guest Name</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Guest Category</label>
                <select name="category_id" id="category_id"
                        class="form-control @error('category_id') is-invalid @enderror">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Branch Dropdown for role_id 1 and 2 --}}
            @if(in_array(Auth::user()->role_id, [1,2]))
            <div class="mb-3">
                <label for="branch_id" class="form-label">Select Branch</label>
                <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                    <option value="">-- Select Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @endif

            <div class="guest-footer">
                <a href="{{ route('guests.index') }}" class="btn btn-back">Back</a>
                <button type="submit" class="btn btn-save">Save Guest</button>
            </div>
        </form>
    </div>
</div>

<style>
    .guest-card {
        border: 1px solid #a2c4f5; /* light blue border like screenshot */
        border-radius: 4px;
        padding: 0;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.1);
        margin: 0 auto;
    }

    .guest-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: #8b0000; /* dark red text */
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }

    .guest-form {
        padding: 15px 20px;
        background: #f9f9f9;
    }

    .guest-footer {
        background-color: #8b0000; /* dark red footer */
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-back {
        background-color: #6c757d; /* gray */
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 3px;
        text-decoration: none;
    }

    .btn-back:hover {
        background-color: #5a6268;
    }

    .btn-save {
        background-color: #007bff; /* blue */
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 3px;
    }

    .btn-save:hover {
        background-color: #0069d9;
    }
</style>
@endsection
