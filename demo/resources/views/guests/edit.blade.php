@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 1px solid #a0c4ff;
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
         max-width: 90%; 
        margin: 40px auto;
        border-radius: 6px;
    }
    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        color: darkred;
        padding: 12px;
        border-bottom: 1px solid #ccc;
        font-size: 18px;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
    }
    .form-input, .form-select {
        width: 100%;
        padding: 8px 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-top: 4px;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .win-footer {
        background: darkred;
        color: white;
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
    }
    .btn-primary {
        background: #1d4ed8;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 14px;
    }
    .btn-secondary {
        background: gray;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 4px;
        font-size: 14px;
    }
</style>

<div class="win-card">
    <div class="win-header">Edit Guest</div>

    <form action="{{ route('guests.update', $guest->id) }}" method="POST" class="p-4">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Guest Name</label>
            <input type="text" name="name" id="name" 
                   class="form-input @error('name') is-invalid @enderror" 
                   value="{{ old('name', $guest->name) }}" required>
            @error('name') <div style="color:red;font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="category_id">Guest Category</label>
            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $guest->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <div style="color:red;font-size:13px;">{{ $message }}</div> @enderror
        </div>

        <div class="win-footer">
            <a href="{{ route('guests.index') }}" class="btn-secondary">Back</a>
            <button type="submit" class="btn-primary">Update Guest</button>
        </div>
    </form>
</div>
@endsection
