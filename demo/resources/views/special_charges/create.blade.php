@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Add Special Charge</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


    <form action="{{ route('special-charges.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="branch_id" class="form-label">Branch</label>
            <select name="branch_id" id="branch_id" class="form-select" required>
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
            @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="special_charge" class="form-label">Special Charge</label>
            <input type="number" step="0.01" name="special_charge" id="special_charge" class="form-control"
                   value="{{ old('special_charge') }}" required>
            @error('special_charge') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('special-charges.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
