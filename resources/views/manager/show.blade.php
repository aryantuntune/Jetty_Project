@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card win-card">
        <div class="win-header">Manager Details</div>
        <div class="p-4">
            <p><strong>Name:</strong> {{ $manager->name }}</p>
            <p><strong>Email:</strong> {{ $manager->email }}</p>
            <p><strong>Mobile:</strong> {{ $manager->mobile ?? 'N/A' }}</p>
            <p><strong>Branch:</strong> {{ $manager->branch?->branch_name ?? 'N/A' }}</p>
            <p><strong>Ferryboat:</strong> {{ $manager->ferryboat?->name ?? 'N/A' }}</p>

            <div class="mt-3">
                <a href="{{ route('manager.edit', $manager) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('manager.destroy', $manager) }}" method="POST" style="display:inline-block;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Delete this manager?')">Delete</button>
                </form>
                <a href="{{ route('manager.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
