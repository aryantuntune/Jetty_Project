@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card win-card">
        <div class="win-header">Operator Details</div>
        <div class="p-4">
            <p><strong>Name:</strong> {{ $operator->name }}</p>
            <p><strong>Email:</strong> {{ $operator->email }}</p>
            <p><strong>Mobile:</strong> {{ $operator->mobile ?? 'N/A' }}</p>
            <p><strong>Branch:</strong> {{ $operator->branch?->branch_name ?? 'N/A' }}</p>
            {{-- <p><strong>Ferryboat:</strong> {{ $operator->ferryboat?->name ?? 'N/A' }}</p> --}}

            <div class="mt-3">
                <a href="{{ route('operator.edit', $operator) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('operator.destroy', $operator) }}" method="POST" style="display:inline-block;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Delete this operator?')">Delete</button>
                </form>
                <a href="{{ route('operator.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
