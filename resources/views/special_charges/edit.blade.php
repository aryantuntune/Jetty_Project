@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Edit Special Charge</h2>

    <form action="{{ route('special-charges.update', $specialCharge->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="branch_id" class="form-label">Branch</label>
            <select name="branch_id" id="branch_id" class="form-select" required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branch->id == old('branch_id', $specialCharge->branch_id) ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
            @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="special_charge" class="form-label">Special Charge</label>
            <input type="number" step="0.01" name="special_charge" id="special_charge"
                   value="{{ old('special_charge', $specialCharge->special_charge) }}" class="form-control" required>
            @error('special_charge') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('special-charges.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
