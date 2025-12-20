@extends('layouts.app')

@section('content')
<style>
    .win-card {
        border: 2px solid #9ec5fe;
        background: #f8fafc;
        box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
        border-radius: 10px;
    }
    .win-header {
        background: #fff;
        text-align: center;
        font-weight: bold;
        font-size: 1.2rem;
        padding: 10px;
        border-bottom: 2px solid #9ec5fe;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
</style>

<div class="container">
    <div class="card win-card">
        <div class="win-header">Administrator Details</div>
        <div class="p-4">
            <p><strong>Name:</strong> {{ $admin->name }}</p>
            <p><strong>Email:</strong> {{ $admin->email }}</p>
            <p><strong>Mobile:</strong> {{ $admin->mobile ?? 'N/A' }}</p>
            <p><strong>Branch:</strong> {{ $admin->branch?->branch_name ?? 'N/A' }}</p>
            <p><strong>Ferryboat:</strong> {{ $admin->ferryboat?->name ?? 'N/A' }}</p>

            <div class="mt-3">
          
                <a href="{{ route('admin.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection
